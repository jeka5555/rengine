<?php

namespace Core\Nodes;

class Site extends \Node {

    // Component
    // ---------
    public static $component = array(
        'id' => 'site',
        'title' => 'Сайт',
    );



    // Node data format
    // ----------------
    public static function getNodeDataFormat() {
        return array(

            // Main
            // ----
            'alias' => array('type' => 'text', 'title' => 'Псевдоним', 'hint' => 'Этот псевдонм используется для идентификации сайта внутри виджетов или шаблонлв. Также идентификатор проставляется в шаблоны выводимых страниц.', 'listing' => true, 'sortable' => true),
            'baseHost' => array('type' => 'text', 'title' => 'Базовый хост'),
            'protocol' => array('type' => 'select', 'title' => 'Протокол', 'values' => array(
                'http' => 'HTTP',
                'https' => 'HTTPS'
            )),

            // Settings
            // --------
            'favicon' => array('type' => 'text', 'title' => 'Иконка сайта'),
            'encoding' => array('type' => 'select', 'values' => array('utf-8' => 'UTF-8', 'win1251' => 'Windows-1251', 'koi8r' => 'KOI8-r'), 'default' => 'utf-8', 'title' => 'Основная кодировка'),

            // Aliases
            // -------
            'aliases' => array('type' => 'list', 'title' => 'URI-альясы', 'format' => array(
                'type' => 'record', 'format' => array(
                    'alias' => array('type' => 'text', 'title' => 'Путь'),
                    'mode' => array('type' => 'select', 'title' => 'Вид проверки', 'values' => array(
                        'text' => 'Текст',
                        'regexp' => 'Регулярное выражение'
                    )),
                    'node' => array('type' => 'object', 'title' => 'Целевой узел', 'class' => 'node'),
                    'redirect' => array('type' => 'boolean', 'title' => 'Делать редирект')
                )
            )),
            'indexNode' => array('type' => 'object', 'class' => 'node', 'title' => 'Главная нода')
        );
    }

    // Node color
    // ----------
    public static $nodeColor = '#FFD46E';

    // Get URI
    // -------
    public function getURI() {
        return first_var(@ $this->data['baseHost'], 'http://'.$_SERVER['SERVER_NAME']);
    }

    // Get children URI
    // ----------------
    public function getChildrenURI($children) {

        // Children path
        // -------------
        $childrenPath = first_var(@ $children->path, @ $children->_id);

        return $this->getURI().'/'.$childrenPath;
    }


    // Process path
    // ------------
    public function processPath($path, $index = 0) {

        // protocol
        // --------
        if (@$this->data['protocol'] == 'https' and (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] != 'on')) {
            \Events::send('setLocation', $this->data['protocol'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        }

        // Set title
        // ---------
        if (!empty($this->title)) {
            \Core::getModule('sites')->setTitle($this->title);
        }

        // Set favicon
        // ------------
        if (!empty($this->favicon)) {
            \Core::getModule('sites')->favicon = $this->favicon;
        }

        // Add breadcrumbs
        // ---------------
        $breadcrumbsModule = \Core::getModule('breadcrumbs');
        if (!empty($breadcrumbsModule)) {
            $breadcrumbsModule->appendbreadcrumbs(array('id' => $this->_id, 'title' => 'Главная страница', 'link' => '/'));
        }


        // Process path
        // ------------
        parent::processPath($path, -1);

    }

    // Execute
    // -------
    public function executeNode() {

        if (!empty($this->data['indexNode'])) {
            $nodeClass = \Core::getClass('node');
            $indexNode = $nodeClass::findPK($this->data['indexNode']);
            $indexNode = \Node::getNodeObject($indexNode);


            $path = str_replace($indexNode->getURI(), '', $indexNode->getURI());
            $indexNode->processPath(array(), 0);

        }

    }
}
