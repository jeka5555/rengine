<?php

namespace Modules;

class Sites extends \Module {

    public static $component = array('id' => 'sites', 'title' => 'Сайты', 'hasSettings' => true);

    public $siteTitle = '';
    public $title = '';
    public $nodes = array(); // List of executed site nodes
    public $currentNode = null; // Current node


    // Component settings
    // ------------------
    public static $componentSettingsFormat = array(
        'titleMode' => array('type' => 'select', 'title' => 'Режим добавления ключевых слов по-умолчанию', 'values' => array(
            'append' => 'Дополнять',
            'replace' => 'Замещать',
            'replaceButKeepSiteTile' => 'Замещать, сохраняя имя сайта'
        )),
        'titleDivider' => array('type' => 'text', 'title' => 'Разделитель для элементов имени')
    );

    // Settings
    // --------
    public static $settings = array(
        'titleMode' => 'append',
        'titleDivider' => ' / '
    );

    public function actionSetPath($args = array()) {

        $_SERVER['REQUEST_URI'] = $args['path'];
        $parse_url = parse_url($_SERVER['REQUEST_URI']);

        if (!empty($parse_url['query'])) {
            $_SERVER['QUERY_STRING'] = $parse_url['query'];
            parse_str($_SERVER['QUERY_STRING'], $_REQUEST);
        }


        // Create new request
        // ------------------
        $requestClass = \Core::getComponent('component', 'request');
        $request = $requestClass::getInstance(array(
            'uri' => $args['path'],
        ));

        // Process
        // -------
        \Core::getApplication()->request->path = $request->path;
        \Core::getApplication()->data['assets']['js'] = \Loader::$files['js'];
        $result = \Core::getApplication()->route($request);
        \Core::getApplication()->data['title'] = \Core::getModule('sites')->title;


        return array(
            'content' => \Widgets::get('block', array('id' => 'content')),
            'menu' => \Widgets::get('block', array('id' => 'menu'))
        );
    }

    // Set title
    // ---------
    public function setTitle($title) {
        $this->title = $title;
    }

    // Append title
    // ------------
    public function appendTitle($title = null) {
        // Return
        // ------
        if (empty($title)) return;

        switch (static::$settings['titleMode']) {
            case 'replace':
                $this->title = $title;
                break;

            case 'append':
                $this->title .= static::$settings['titleDivider'].$title;
                break;
        }
    }

    // Get node data format
    // --------------------
    public function actionGetNodeDataFormat($type = null) {

        if(empty($type)) return null;
        $nodeComponent = \Core::getComponent('node', $type);

        // Init
        // ----
        $properties = $nodeComponent::getNodeDataFormat();
        $structure = $nodeComponent::getNodeDataStructure();

        // Return
        // ------
        return array(
            'properties' => $properties,
            'structure' => $structure
        );
    }

}
