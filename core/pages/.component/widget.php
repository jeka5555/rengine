<?php

class Widget extends \Component {

    public static $component = array(
        'type' => 'component',
        'id' => 'widget',
        'title' => 'Виджет',
        'autoload' => true
    );


    public static $widgetArgsFormat = array(); // Widget arguments format
    public static $access = array();  // Информация для доступа
    public static $import = array();  // Загружаемые данные

    public $css = array(); // Styles

    // Cancel ouptut
    // -------------
    public function cancel() {
        $this->cancelled = true;
    }

    // Get args format
    // ---------------
    public function getWidgetArgsFormat() {

        // Get widget
        return @static::$widgetArgsFormat;
    }

    // Get HTML Attributes
    // ------------------------
    public function getHTMLAttributes() {
        return first_var(@ $this->options['htmlAttributes'], array());
    }

    // Attach object
    // -------------
    public function attachObject($args = array()) {

        // Edit mode
        // ---------
        if (empty($args['class']) || empty($args['id']) || \Core::getApplication()->data['editMode'] != true) return false;

        // Get object data
        // ---------------
        $class = \Core::getClass($args['class']);
        if (empty($class)) return;

        // Если нет, то выход
        // ------------------
        if (!$class::checkClassAccess('edit')) return;

        $htmlID = $this->generateHtmlID();

        // Arguments
        // ---------
        $args = array(
            'class' => $args['class'],
            'id' => $args['id'],
            'widget' => '#'.$htmlID,
            'useWrapper' => true
        );

        // Add script
        // ----------
        \Events::send('addEndScript', 'new ObjectController('.json_encode($args).');');
    }

    // Create identifier
    // -----------------
    public function generateHtmlID() {

        // Already generated
        // -----------------
        if (!empty($this->htmlID)) return $this->htmlID;

        // From ID
        // -------
        if (!empty($this->options['htmlID'])) {
            $this->htmlID = $this->options['htmlID'];
        }

        // If widget has ID
        // ----------------
        else if (!empty($this->_id)) {
            $this->htmlID =  'widget-'.$this->_id;
        }
        // Some random
        // -----------
        else {
            $this->htmlID = 'widget-'.(string) new MongoID();
        }

        return $this->htmlID;

    }


    // Generate list of html classes
    // -----------------------------
    public function generateHtmlClasses() {

        $type = static::$component['id'];

        // Base classes set
        // ----------------
        if (is_array(@ $this->options['htmlClasses'])) $htmlClasses = $this->options['htmlClasses'];
        else if (is_string(@ $this->options['htmlClasses'])) $htmlClasses = explode(" ", $this->options['htmlClasses']);
        else $htmlClasses = array();

        // Standart widget classes
        // -----------------------
        $htmlClasses[] = 'widget-'.$type;
        $htmlClasses[] = 'widget';

        return $htmlClasses;
    }

    // Wrap widget to tag
    // ------------------
    public function wrap($content) {

        // Hide cancelled widgets
        // ----------------------
        if (@ $this->cancelled == true) return;

        // Only content
        // ------------
        if (@ $this->options['update'] === true || @ $this->options['tag'] === false) return $content;

        // Default tag
        // -----------
        $tag = first_var(@ $this->options['tag'], @static::$component['tag'], 'div');

        // Wrappers
        // --------
        if (!empty($this->options['wrappers'])) {
            $wrappers = array_reverse($this->options['wrappers']);
            foreach($wrappers as $wrapper) {
                $content  = '<div class="'.$wrapper.'">'.$content.'</div>';
            }
        }


        // Attributes
        // ----------
        $htmlAttributes = $this->getHTMLAttributes();

        // Edit mode
        // ---------
        if ($this->editMode == true) {
            $htmlAttributes = array_merge($htmlAttributes, array(
                'data-widget-id' => @ $this->_id,
                'data-block' => @ $this->options['block'],
                'data-order' => @ $this->options['order']
            ));
        }

        // Если задан тэг, оборачиваем данные
        // ----------------------------------
        if ($tag != false) {
            $content = \Content::buildTag(array(
                'htmlID' => $this->generateHtmlID(),
                'htmlClasses' => $this->generateHtmlClasses(),
                'htmlAttributes' => @ $htmlAttributes,
                'tag' => @ $tag,
                'css' => @ $this->options['css'],
                'content' => $content
            ));
        }

        // Прикрепляем контроллер
        // ----------------------
        return $content;
    }


    // Import assets
    // -------------
    public function importAssets() {

        // Скрипты
        // -------
        if (!empty(static::$import['js'])) {
            foreach(static::$import['js'] as $js) {
                \Events::send('addJS', '/'.static::$component['componentPath'].'/'.$js);
            }
        }

        // Стили
        // -----
        if (!empty(static::$import['css'])) {
            foreach(static::$import['css'] as $css) {
                \Events::send('addCSS', '/'.static::$component['componentPath'].'/'.$css);
            }
        }

    }


    // Add widget controller
    // ---------------------
    public function addControllerScript() {

        // Is user allowed to edit this?
        // -----------------------------
        $editorUser = \Rules::check(array(
            array('type' => 'or', 'rules' => array(
                array('type' => 'userRole', 'role' => 'administrator'),
                array('type' => 'userRole', 'role' => 'super'),
            ))
        ));

        // Check for edit mode
        // --------------------
        if (!$editorUser || empty($this->_id) || \Core::getApplication()->data['editMode'] != true) return false;

        // Get ID
        // ------
        $htmlID = $this->generateHtmlID();

        // Type title
        // ----------
        $typeTitle = first_var(@ static::$component['title'], @ static::$component['id']);

        // Get script properties
        // ---------------------
        $controllerArgs = array(
            'type' => @ $this->type,
            'block' => @ $this->options['block'],
            'title' => first_var(@ $this->title, 'без названия'),
            'widgetTypeTitle' => $typeTitle,
            'widget' => '#'.$htmlID,
            'id' => @ $this->id
        );

        // Submit script
        // -------------
        \Events::send('addEndScript', 'widgetController'.str_replace($htmlID,'-','').' = new WidgetController('.json_encode($controllerArgs).');');

    }


    // Get widget with whole features
    // ------------------------------
    public function get() {

        if (!empty(\Core::getModule('users')->user) and \Core::getModule('users')->user->hasPermission('debugging')) {
            $timer = new \Timer();
        }

        // Check for edit mode
        // -------------------------
        $this->editMode  = (!empty($this->_id) && \Rules::checkRule(array('type' => 'userRole', 'role' => 'administrator')));

        // Check for visibility
        // --------------------
        if (!\Rules::checkRule(array('type' => 'userRole', 'value' => 'administrator'))) {

            // If widget is disabled
            // ---------------------
            if (@ $this->options['disabled'] === true) return false;

            // If it doesn't check for visibility rules
            // ----------------------------------------
            if (!empty($this->options['visibility'])) {
                if (!\Rules::check($this->options['visibility'])) return false;
            }
        }

        // Disabled widgets are hidden
        // ---------------------------
        if (@ $this->options['disabled'] === true) return;

        // Try to use cache
        // ----------------
        if (@ $this->options['cache']['enabled'] == true && !empty($this->_id)) {

            // Get cache module
            // ----------------
            $cacheModule = \Core::getModule('cache');
            // Build key
            // ---------
            $options = first_var( @ $this->options['cache']['options'], array());
            $options['id'] = $this->_id;

            // Get exporation time
            // -------------------
            $expiration = 10000000;
            if (!empty($cacheOptions['expiration'])) {
                $expiration = $cacheOptions['expiration'];
            }

            // If exists, read from cache
            // --------------------------
            if ($cacheModule->exists($options, $expiration, true)) {
                $content = $cacheModule->pop($options, $expiration);
            }

            // Or store in cache
            // -----------------
            else {
                $content = $this->render();
                $cacheModule->push($content, $options, $expiration);
            }


        }

        // Or render it
        // ------------
        else {
            $content = $this->render();
        }

        // Apply templates
        // ---------------
        if (is_array($content) || is_object($content)) {

            // Detect correct template to apply
            // --------------------------------
            $templateID = first_var(@$this->options['template'], @ static::$component['template']);
            $templateExtension = first_var(@ \Extension::$ext['template'][$templateID], @ \Extension::$ext['template']['widget-'.static::$component['id']]);

            // Если нет шаблона
            // ----------------
            if (empty($templateExtension)) {
                \Logs::log('Не найден шаблон для виджета '. static::$component['id'], 'warning');
                return false;
            }

            // Pass though template
            // --------------------
            $content = \Templates::get($templateExtension['id'], $content);
            $this->options['htmlClasses'][] = ' template-'.$templateExtension['id'];

        }

        // Если есть контент для вывода до или после
        // ----------------
        if(!empty($this->options['contentBefore'])) {
            $content = '<div class="content-before">'.$this->options['contentBefore'].'</div>'.$content;
        }
        if(!empty($this->options['contentAfter'])) {
            $content .= '<div class="content-after">'.$this->options['contentAfter'].'</div>';
        }


        // Wrap final content
        // ------------------
        $content =  $this->wrap($content);

        // Add scripts and resources
        // -------------------------
        if (@$this->options['notAddControllerScript'] != true)
            $this->addControllerScript();

        if (@$this->options['notImportAssets'] != true)
            $this->importAssets();

        if (!empty(\Core::getModule('users')->user) and \Core::getModule('users')->user->hasPermission('debugging')) {
            //echo get_class($this).': '.$timer->finish();
        }

        // Return content
        // --------------
        return $content;
    }


    // Визуализация виджета
    // --------------------
    public function render() {
        \Logs::log('Виджет '.$this->type.' не существует', 'warning');
        return '';
    }

    // Send to output
    // ---------------
    public function out() {
        $id = first_var(@ $this->_id, uniqid());
        \Core::getModule('widgets')->widgets[$id] = $this;
    }

    public function preRender() {
    }
    public function postRender() {
    }






}
