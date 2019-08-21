<?php


namespace Postyou\WebpackEncoreRemoteBundle\Conversion;

use Contao\CoreBundle\Monolog\ContaoContext;
use FilesystemIterator;
use Psr\Log\LogLevel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\RequestOptions;
use Postyou\WebpackEncoreRemoteBundle\Model\EncoreEntryModel;
use ZipArchive;

class Converter extends \Backend
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_encore_converter';

    public function __construct() {
        parent::__construct();
    }

    public function doConversion($withRedirect = true)
    {

        $authData = [\Config::get('username'), \Config::get('password')];
        $key = str_replace('=', '', base64_encode($authData[0].':'.$authData[1]));

        $client = new Client([
            'base_uri' => 'https://webpack.postyou.de',
        ]);

        $buildFolder = TL_ROOT. '/' .\FilesModel::findByUuid(\Config::get('buildFolder'))->path;

        $fp = fopen( $buildFolder . '/build.zip','w');



        $response = NULL;
        try {
            $mode = 'dev';
            if (!empty(\Config::get('webpack-encore-mode'))) {
                $mode = \Config::get('webpack-encore-mode');
            }

            $response = $client->request('POST', '/runwithconfig/' . $mode, [
                RequestOptions::AUTH => $authData,
                RequestOptions::MULTIPART =>
                    $this->createMultiPartArr($key)

            ]);

            $response = $client->request('GET', '/getbuildzip/', [
                RequestOptions::AUTH => $authData,
                RequestOptions::SINK => $fp
            ]);

            if ($withRedirect) {
                \Controller::redirect(\Controller::addToUrl('fine=1',true,array('key', 'error')));
            }

        } catch (RequestException $e) {
            $logger = \System::getContainer()->get('monolog.logger.contao');
            $exceptionContent = $e->getResponse()->getBody()->getContents();
            $logger->error($exceptionContent);
            $logger
                ->log(LogLevel::ERROR, $exceptionContent, array(
                    'contao' => new ContaoContext(__CLASS__.'::'.__FUNCTION__, TL_ERROR
                    )));

            if ($withRedirect) {
                \Controller::redirect(\Controller::addToUrl('error=1',true,array('key', 'fine')));
            }

        } finally {
            fclose($fp);
            $zip = new ZipArchive();
            if ($zip->open($buildFolder . '/build.zip') === TRUE) {
                $directoryIterator = new RecursiveDirectoryIterator( $buildFolder, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS );
                foreach( new RecursiveIteratorIterator($directoryIterator,  RecursiveIteratorIterator::CHILD_FIRST ) as $value ) {
                    if (strpos($value, '.public') === false)
                        $value->isFile() ? unlink( $value ) : rmdir( $value );
                }

                $zip->extractTo( $buildFolder);
                $zip->close();
                if (file_exists($buildFolder . '/build.zip')) {
                    unlink($buildFolder . '/build.zip');
                }

                if (file_exists(TL_ROOT.'/files_to_build.zip')) {
                    unlink(TL_ROOT.'/files_to_build.zip');
                }

            } else {
                if ($withRedirect) {
                    \Controller::redirect(\Controller::addToUrl('error', true, array('key', 'fine')));
                }
            }
        }

    }

    private function createMultiPartArr($key) {
        $models = EncoreEntryModel::findAll();


        $multiPartArr = [];

        //Config File
        $configStr = trim(
            '/**Start'.$key.'*/
            var Encore = require("@symfony/webpack-encore");
            Encore
            .setOutputPath("'.$key.'/build/")
            .setPublicPath("/'.$key.'/build")'
        );

        while ($models->next()) {
            $path = \FilesModel::findByUuid($models->path)->path;
            $configStr .= '.addEntry("'.$models->name.'","./'.$key.'/'.$path.'")';
        }

        if (empty(\Config::get('extendedWebpackEncoreConfiguration'))) {
            $defaultStr = '.enableSingleRuntimeChunk()
                .cleanupOutputBeforeBuild()
                .enableSourceMaps(!Encore.isProduction())
                .enableVersioning(Encore.isProduction())
                .enablePostCssLoader();';
            $configStr .= trim(preg_replace('/[^\S\r\n]/m', '', $defaultStr));
        } else {
            $configStr .= \Config::get('extendedWebpackEncoreConfiguration');
        }



        $configStr.= trim(
            'const '.$key.' = Encore.getWebpackConfig();
            '.$key.'.name = "'.$key.'";
            Encore.reset();
            /**End'.$key.'*/'
        );

        $multiPartArr[] = [
            'name'     => 'config',
            'contents' => Psr7\stream_for($configStr),
            'filename' => 'webpack.config.js',
            RequestOptions::HEADERS => ['Content-Type' => 'application/javascript']
        ];


        //Assets Folder
        $assetsFolder = TL_ROOT.'/'.\FilesModel::findByUuid(\Config::get('assetsFolder'))->path;
        $this->zipFolder($assetsFolder, TL_ROOT.'/files_to_build.zip');

        $multiPartArr[] = [
            'name' => 'build_files',
            'contents' => Psr7\stream_for(file_get_contents(TL_ROOT.'/files_to_build.zip')),
            'filename' => 'files_to_build.zip'
        ];


        return $multiPartArr;
    }

    private function zipFolder($folderPath, $fileName) {

        $rootPath = realpath($folderPath);


        $zip = new ZipArchive();
        $zip->open($fileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);


        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $name => $file)
        {

            if (!$file->isDir())
            {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strrpos($rootPath, 'files/'));

                $zip->addFile($filePath, $relativePath);
            }
        }


        $zip->close();
    }
}
