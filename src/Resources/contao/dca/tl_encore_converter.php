<?php



$GLOBALS['TL_DCA']['tl_encore_converter'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'File',
        'closed'                      => true,
    ),

    // Palettes
    'palettes' => array
    (
        'default'                     => 'assetsFolder,buildFolder,webpack-encore-mode,username,password,html;{expert_legend:hide},extendedWebpackEncoreConfiguration;'
    ),

    // Fields
    'fields' => array
    (

        'html' => array
        (
            'input_field_callback'    => function() {

                \System::loadLanguageFile('tl_encore_converter');
                $returnHTML = '
                <div class="clr widget" style="padding-top: 20px;">
                    <a href="'.ampersand(\Environment::get('request')).'&key=doConversion" class="tl_submit">'.$GLOBALS['TL_LANG']['tl_encore_converter']['submit'][0].'</a>
                </div>
                <div class="message clr widget" >
                ';
                if (\Input::get('fine')) {
                    $returnHTML .= '<p class="tl_confirm">'.$GLOBALS['TL_LANG']['tl_encore_converter']['confirm'].'</p>';
                } else if (\Input::get('error')) {
                    $returnHTML .= '<p class="tl_error">'.$GLOBALS['TL_LANG']['tl_encore_converter']['error'].'</p>';
                }
                $returnHTML.= '</div>';
                return $returnHTML;

            }
        ),
        'webpack-encore-mode' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_converter']['webpack-encore-mode'],
            'exclude'                 => true,
            'inputType'               => 'radio',
            'options'                 => array('dev', 'production'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_encore_converter'],
            'eval'                    => array( 'tl_class'=>'clr', 'submitOnChange' => true),
            'sql'                     => "char(200) NOT NULL default 'dev'"
        ),
        'username' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_converter']['username'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array( 'tl_class'=>'w50'),
            'save_callback'           => array(function($value) {
                return html_entity_decode($value, ENT_QUOTES | ENT_XML1, 'UTF-8') ;
            }),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'password' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_converter']['password'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array( 'tl_class'=>'w50'),
            'save_callback'           => array(function($value) {
                return html_entity_decode($value, ENT_QUOTES | ENT_XML1, 'UTF-8') ;
            }),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'extendedWebpackEncoreConfiguration' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_converter']['extendedWebpackEncoreConfiguration'],
            'exclude'                 => true,
            'inputType'               => 'textarea',
            'eval'                    => array('allowHtml'=>true, 'class'=>'monospace', 'rte'=>'ace'),
            'load_callback'           => array(function($value, $dc) {
                if (empty($value)) {
                    return trim(preg_replace('/[^\S\r\n]/m', '', '
                                                    .enableSingleRuntimeChunk()
                                                    .cleanupOutputBeforeBuild()
                                                    .enableSourceMaps(!Encore.isProduction())
                                                    .enableVersioning(Encore.isProduction())
                                                    .enablePostCssLoader();'));
                }
                return $value;
            }),
            'sql'                     => "mediumtext NULL"
        ),
        'assetsFolder' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_converter']['assetsFolder'],
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('multiple' => false, 'files' => false, 'filesOnly'=>false, 'fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "blob NULL"
        ),
        'buildFolder' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_encore_converter']['buildFolder'],
            'exclude'                 => true,
            'inputType'               => 'fileTree',
            'eval'                    => array('multiple' => false, 'files' => false, 'filesOnly'=>false, 'fieldType'=>'radio', 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "blob NULL"
        ),
    )
);
