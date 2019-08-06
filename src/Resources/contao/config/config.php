<?php

if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS'][] = 'bundles/postyouwebpackencoreremote/css/backend.css';
}

$GLOBALS['BE_MOD']['webpack-encore']['encoreconverter'] = [
    'doConversion'		=> ['Postyou\WebpackEncoreRemoteBundle\Conversion\Converter', 'doConversion'],
    'tables' => ['tl_encore_converter']
];

$GLOBALS['BE_MOD']['webpack-encore']['encoreentries'] = [
    'tables' => ['tl_encore_entries']
];

$GLOBALS['TL_MODELS']['tl_encore_entries'] = 'Postyou\WebpackEncoreRemoteBundle\Model\EncoreEntryModel';
