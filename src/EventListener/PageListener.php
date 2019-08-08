<?php

namespace Postyou\WebpackEncoreRemoteBundle\EventListener;

use Contao\StringUtil;
use Postyou\WebpackEncoreRemoteBundle\Model\EncoreEntryModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class PageListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function onGeneratePage($objPage, &$objLayout, $objPageRegular) {
        $arrEntries = array_unique($this->findEncoreEntries($objPage, array()));

        $buildPath = \FilesModel::findByUuid(\Config::get('buildFolder'))->path;


        $entryPoints = new \File($buildPath.'/entrypoints.json');

        if ($entryPoints->exists()) {
            $entryPointsArr = \GuzzleHttp\json_decode($entryPoints->getContent(), true);
        }

        foreach ($arrEntries as $arrEntry) {
            $model = EncoreEntryModel::findById($arrEntry);
            if (isset($entryPointsArr['entrypoints'][$model->name])) {
                foreach ($entryPointsArr['entrypoints'][$model->name] as $key => $file) {
                    if ($key == 'js') {
                        foreach ($file as $jsFile) {
                            $GLOBALS['TL_JAVASCRIPT'][] = $buildPath.substr($jsFile, strrpos($jsFile, '/'));
                        }
                    } else if ($key == 'css') {

                        //NOTE Use of external Stylesheet needed to place the css after contao framework/reset CSS
                        $tmpArr = [];

                        if (empty($objLayout->external)) {
                            $objLayout->external = [];
                        } else {
                            $tmpArr = \StringUtil::deserialize($objLayout->external);
                        }
                        foreach ($file as $id => $cssFile) {
//                            $GLOBALS['TL_CSS']['webpack_css_'.$id] = $buildPath.substr($cssFile, strrpos($cssFile, '/'));
                            $filesModel = \Dbafs::addResource($buildPath.substr($cssFile, strrpos($cssFile, '/')));
                            $tmpArr[] = $filesModel->uuid;
                        }
                        $objLayout->external = serialize($tmpArr);
                    }
                }
            }
        }


    }

    private function findEncoreEntries($objPage, $arrEntries) {
        $parentPage = \PageModel::findById($objPage->pid);

        if (isset($objPage->encore_entries)) {
            $entryArr = unserialize($objPage->encore_entries);
            foreach($entryArr as $entry) {
                $arrEntries[] = $entry;
            }
        }

        if (isset($parentPage)) {
            return $this->findEncoreEntries($parentPage, $arrEntries);
        }

        return $arrEntries;
    }

}
