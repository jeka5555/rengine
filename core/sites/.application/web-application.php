<?php

namespace Core\Sites\Applications;

class WebApplication extends \Core\Sites\Components\Application {

	// Component
	// ---------
	public static $component = array(
		'type' => 'application',
		'id' => 'web-application',
		'title' => 'Базовое web-приложение'
	);

	// Data
	// ----
	public $data = array(
		'script' => array(),
		'endScript' => array(),
		'events' => array()
	);

	// Router
	// ------
	public $routerClass = 'rework';

	// Variables
	// ---------
	public $request = null; // Request object

	// Get instance
	// ------------
	public static function getInstance($args = array()) {

		// Create instance
		// ---------------
		$instance = parent::getInstance($args);

		// Add request
		// -----------
		$requestClass = \Core::getComponent('component', 'request');
		$instance->request = $requestClass::getInstance();

		return $instance;
	}

    // Run application
    // ---------------
    public function runApplication() {

        \Events::send('applicationStart');

        // Create request
        // --------------
        $requestClass = \Core::getComponent('component', 'request');
        $headers = $requestClass::parseHeaders();

        // Current URI
        // -----------
        $currentURI = $_SERVER['REQUEST_URI'];

        // Edit mode
        // ---------
        $this->data['editMode'] = @ $_SESSION['editMode'];

        // Create request
        // --------------
        $this->request = $requestClass::getInstance(array(
            'isAJAX' => (@ $headers['REQUEST-MODE'] == 'API'),
            'uri' => $currentURI
        ));


        // Set data from request or from parsed data
        // -----------------------------------------
        if ($this->request->isAJAX == true) {

            // Get data if any exists
            // ----------------------
            if (!empty($_REQUEST['data'])) {
                $this->request->data = json_decode($_REQUEST['data'], true);
            }

            // If location is present
            // ----------------------
            if (!empty($_REQUEST['location'])) {
                $this->request->location = $_REQUEST['location'];
            }

            // Route
            // -----
            \Events::send('applicationRoute');
            $result = $this->route($this->request);
            \Events::send('applicationRouteFinish');
            $this->renderJSON($result);
        }

        // Not AJAX Requests
        // -----------------
        else {
            $this->request->data = $_REQUEST;
            \Events::send('applicationRoute');

            // Route
            // -----
            $result = $this->route($this->request);


            if (!empty($this->data['location'])) {
                header('Location: '.$this->data['location']); die();
            }

            // Render page
            // -----------
            \Events::send('applicationRouteFinish');
            $this->renderPage();
        }

        \Events::send('applicationFinish');
    }


    // Reload page
    // -----------
    public function reloadPage($path) {

        // Create request
        // --------------
        $requestClass = \Core::getComponent('component', 'request');
        $headers = $requestClass::parseHeaders();

        // Current URI
        // -----------
        $currentURI = $path;

        // Create request
        // --------------
        $this->request = $requestClass::getInstance(array( 'isAJAX' => true, 'uri' => $currentURI ));

        // Get data if any exists
        // ----------------------
        if (!empty($_REQUEST['data'])) {
            $this->request->data = json_decode($_REQUEST['data'], true);
        }

        // If location is present
        // ----------------------
        if (!empty($_REQUEST['location'])) {
            $this->request->location = $_REQUEST['location'];
        }

        // Route
        // -----
        $result = $this->route($this->request);
        $this->renderJSON($result);

    }


	// Route event
	// -----------
	public function dispatchEvent($eventType, $data = null) {

		$methodName = 'event'.$eventType;

		// If event method found, process it
		// ---------------------------------
		if (method_exists($this, $methodName)) {
			call_user_func(array($this, $methodName), $data);
		}

	}

	// Set new location
	// ----------------
	private function eventSetLocation($location = '/') {
		$this->data['location'] = $location;
	}

	public function eventSetPageTitle($title) {
		$this->data['page']['title'] = $title;
	}

	public function eventClientEvent($event) {
		$this->data['events'][] = $event;
	}

	// Submit script to page
	// ---------------------
	public function eventAddScript($script) {
		$this->data['script'][] = $script;

	}

	// Submit script to page
	// ---------------------
	public function eventAddEndScript($script) {
		$this->data['endScript'][] = $script;
	}

	// Reload page
	// -----------
	private function eventReload($args = array()) {
		if (!empty($this->request->location)) {
			$this->data['location'] = $this->request->location;
		}
		else $this->data['location'] = '/';
	}

	// Login event
	// -----------
    public function eventLogin() {
		$this->eventReload();
	}

	// Process route
	// -------------
	public function route($request = null) {

		// API hooks?
		// ----------
		if (@ $request->path[0] == 'module') {
			// If no module ID, return
			// ------------------------
			if (empty($request->path[1])) return;

			// Get module instance
			// -------------------
			$module = \Core::getModule($request->path[1]);
			if (empty($module)) return;

			// Get module action
			// -----------------
			$result = $module->action( $request->path[2], $request->data, $request->path);

			// Return result
			// -------------
			return $result;

		}

		// Create router instance
		// ----------------------
		$routerClass = \Core::getComponent('router', $this->routerClass);
		if (empty($routerClass)) return;
		$router = $routerClass::getInstance();

		// Init router data
		// ----------------
		$router->request = $request;
		return $router->route();

	}


	// Render page
	// -----------
	private function renderPage() {

		// Load page renderer
		// ------------------
		$pageRendererClass = \Core::getComponent('page-renderer', 'default');
		$pageRenderer = $pageRendererClass::getInstance();

		// Prerender content
		// -----------------
		$pageRenderer->prerender();

		// Init data
		// ---------
		$pageRenderer->template = @ $this->data['page']['template'];
		$pageRenderer->renderContent();

		$pageRenderer->htmlClasses = @ $this->data['page']['htmlClasses'];

		// Page-related data
		// -----------------
		$pageRenderer->title = @ \Core::getModule('sites')->title;
		$pageRenderer->favicon = @ \Core::getModule('sites')->favicon;

		// Scripts
		// -------
		$pageRenderer->script = @ $this->data['script'];
		$pageRenderer->endScript = @ $this->data['endScript'];

		// SEO-related data
		// ----------------
		$pageRenderer->keywords = @ \Core::getModule('seo')->keywords;
		$pageRenderer->description = @ \Core::getModule('seo')->description;

		// Collect assets
		// --------------
		$pageRenderer->assets = array(
			'js' => \Loader::$files['js'],
			'css' => \Loader::$files['css'],
		);

        $templateClass = \Core::getClass('template');
        $templateObject = $templateClass::findOne(array('query' => array(
            'id' => $pageRenderer->template
        )));

        if (!empty($templateObject->assets['js']) and is_array($templateObject->assets['js'])) $pageRenderer->assets['js'] = array_merge($pageRenderer->assets['js'], $templateObject->assets['js']);
        if (!empty($templateObject->assets['css']) and is_array($templateObject->assets['css'])) $pageRenderer->assets['css'] = array_merge($pageRenderer->assets['css'], $templateObject->assets['css']);

        $pageRenderer->assets['js'][] = '/core/.assets/js/tinymce/tinymce.min.js';

        if (!empty($templateObject->headContentAddin)) $pageRenderer->headContentAddin = $templateObject->headContentAddin;

		// Render page
		// -----------
		$result = $pageRenderer->render();
		echo($result);

	}


	// JSON to output
	// --------------
	private function renderJSON($result) {

		$output = array();

		// Set page related data
		// ---------------------
		if (!empty($this->data['title'])) $output['title'] = $this->data['title'];
		if (!empty($this->data['pageContent'])) $output['pageContent'] = $this->data['pageContent'];

		if (!empty($this->data['location'])) $output['location'] = $this->data['location'];

		if (!empty($this->data['assets'])) $output['assets'] = $this->data['assets'];
		if (!empty($this->data['events'])) $output['events'] = $this->data['events'];

		if (!empty($this->data['script'])) $output['script'] = $this->data['script'];
		if (!empty($this->data['endScript'])) $output['endScript'] = $this->data['endScript'];

		// Add result
		// ----------
		$output['result'] = $result;

		// Die with result
		// ---------------
		echo(json_encode($output));
        die();
	}

	// Process module request
	// ----------------------
	public function processModuleRequest($request) {


	}


}
