<?php

namespace Postyou\WebpackEncoreRemoteBundle\EventListener;

use Postyou\WebpackEncoreRemoteBundle\Model\EncoreEntryModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class PageListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function onGeneratePage($objPage, $objLayout, $objPageRegular) {
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
                            $GLOBALS['TL_JAVASCRIPT'][] = 'files/'.$jsFile;
                        }
                    } else if ($key == 'css') {
                        foreach ($file as $cssFile) {
                            $GLOBALS['TL_CSS'][] = 'files/'.$cssFile;
                        }
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
