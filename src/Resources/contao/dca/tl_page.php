<?php

$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace('{layout_legend:hide},includeLayout;', '{layout_legend:hide},includeLayout;{encore_legend:hide},encore_entries;', $GLOBALS['TL_DCA']['tl_page']['palettes']['regular']);
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace('{layout_legend},includeLayout;', '{layout_legend},includeLayout;{encore_legend:hide},encore_entries;', $GLOBALS['TL_DCA']['tl_page']['palettes']['root']);

$GLOBALS['TL_DCA']['tl_page']['fields']['encore_entries'] = array(
        'label'                   => &$GLOBALS['TL_LANG']['tl_page']['encore_entries'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'foreignKey'              => 'tl_encore_entries.name',
        'eval'                    => array('multiple' => true, 'tl_class'=>'w50'),
        'relation'                => array('type'=>'hasMany', 'load'=>'lazy'),
        'sql'                     => "blob NULL"
);
