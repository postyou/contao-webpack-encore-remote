<?php

$GLOBALS['TL_DCA']['tl_encore_entries'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'switchToEdit'                => true,
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 1,
//            'disableGrouping'         => true,
            'panelLayout'             => 'filter;search',
            'fields'                  => array('name', 'tstamp')
        ),
        'label' => array
        (
            'fields'                  => array('name'),
//            'format'                  => '%s',
            'showColumns'             => true
        ),
        'global_operations' => array
        (
            'toggleNodes' => array
            (
                'href'                => '&amp;ptg=all',
                'class'               => 'header_toggle',
                'showOnSelect'        => true
            ),
            'all' => array
            (
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'href'                => 'act=edit',
                'icon'                => 'edit.svg',
//                'button_callback'     => array('tl_article', 'editArticle')
            ),
            'copy' => array
            (
                'href'                => 'act=paste&amp;mode=copy',
                'icon'                => 'copy.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
//                'button_callback'     => array('tl_article', 'copyArticle')
            ),
            'cut' => array
            (
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset()"',
//                'button_callback'     => array('tl_article', 'cutArticle')
            ),
            'delete' => array
            (
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
//                'button_callback'     => array('tl_article', 'deleteArticle')
            ),
            'toggle' => array
            (
                'icon'                => 'visible.svg',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
//                'button_callback'     => array('tl_article', 'toggleIcon'),
                'showInHeader'        => true
            ),
            'show' => array
            (
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array('protected'),
        'default'                     => 'name, path'
    ),

//    // Subpalettes
//    'subpalettes' => array
//    (
//        'protected'                   => 'groups'
//    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'label'                   => array('ID'),
            'search'                  => true,
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default 0"
        ),
        'name' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_entries']['name'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'path' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_entries']['path'],
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "binary(16) NULL"
        ),
    )
);
