<?php

class Core extends \Module {

	// Component
	// ---------
	public static $component = array('id' => 'core', 'title' => 'Ядро');

	// Core instance
	// -------------
	public static $coreInstance = null;

	// Application instance
	// --------------------
	public $application = null;

	// Settings
	// --------
	public static $settings = array(
		'language' => 'ru',
		'locale' => 'ru_RU.UTF8',
		'encoding' => 'UTF-8',
		'applicationClass' => 'web-application',
		'useTrash' => true
	);

	// Refresh application state with new request data
	// -----------------------------------------------
	public function refresh() {

		// Get application class
		// ---------------------
		if(!empty(static::$settings['applicationClass'])) {

			$applicationClass = \Core::getComponent('application', static::$settings['applicationClass']);

			// If application class are exists, run it
			// ---------------------------------------
			if (!empty($applicationClass)) {

				// Get application class
				// ---------------------
				$this->application = $applicationClass::getInstance();

				// Run application with events
				// ---------------------------

				$this->application->runApplication();

			}

			// Or just error
			// -------------
			else {
				die('Ошибка запуска.Класс приложения не указан');
			}
		}
	}

	// Register data
	// -------------
	public static function initComponent() {

		if (@static::$component['initialized'] == true ) return;

		// Init component
		// --------------
		parent::initComponent();
		$component = \Core::getModule('core');

		// Read settings and merge
		// -----------------------
        require_once(__DR__.'config.php');
		static::$settings = array_merge(static::$settings, $config);

		// Session
		// -------
		session_set_cookie_params(0);

		// Main settings
		// -------------
		define('__SITE_KEY__', @ $config['key']);
		define('__SITE_ID__', @ $config['id']);

		// Set encoding
		// ------------
		mb_internal_encoding(static::$settings['encoding']);
		setlocale(LC_TIME, static::$settings['locale']);
	}

	// Запуск ядра
	// ----------------------------------
	public function start($args = array()) {

		$packagesClass = \Core::getClass('package');
		$packages = $packagesClass::find(array('query' => array('enable' => true), 'sort' => array('order' => -1)));

		// If config modules are empty
		// ---------------------------
		if (!empty($packages)) {
			// Load each package
			// -----------------
			foreach($packages as $package) {
				\Loader::importPackage($package->id);
			}

		}
		// Init components
		// ---------------
		\Extension::initComponents();
		\Events::send('componentsInitialized');

		// Push messages to client
		// ------------------------
		if (@ static::$settings['underConstruction'] == true) {

			// Get message module
			// ------------------
			$messagesModule = \Core::getModule('flash-messages');

			// Add message text
			// ----------------
			$messageText = first_var(@ static::$settings['underConstructionMessage'], 'Сайт находится в разработке');

			// Add message
			// -----------
			$messagesModule->add(array('pinned' => true,'type' => 'notification','text' => $messageText	));
		}


		// Compile resources
		// -----------------
		\Core::getModule('compiler')->compile();

		// Refresh
		// -------
		\Events::send('coreStart');

		$this->refresh();

		\Events::send('coreStop');

	}


	// Reload page
	// -----------
	public function actionReloadPage($args = array()) {
		\Core::getApplication()->reloadPage($args['location']);
	}


	// Get component by type and ID
	// ----------------------------
	public static function getComponent($type = null, $id = null) {
	  if (!empty($id)) return @ \Extension::$ext[$type][$id]['class'];
		else return @ constant($type);
	}


	// Get module by ID
	// ----------------
	public static function getModule($id = null) {
		if (empty($id)) return null;
		return @ \Modules::get($id);
	}

	// Get class
	// ---------
	public static function getClass($id = null) {
		if (empty($id)) return null;
		return @\Extension::$ext['class'][$id]['class'];
	}

	// Get core instance
	// -----------------
	public static function getInstance($args = array()) {
		if (empty(static::$coreInstance)) static::$coreInstance = parent::getInstance();
		return static::$coreInstance;
	}

	// Call named function with arguments
	// ----------------------------------
	public static function call($name, $args = null) {
		return null;
	}


	// Get current application
	// -----------------------
	public static function getApplication() {
		$coreModule = \Core::getInstance();
		return @ $coreModule->application;
	}

}
