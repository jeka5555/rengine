<?php

class Core extends \Component {

	// Extensions
	// ----------
	public $application = null; // Application object
	public $modules = array(); // Modules

	// Properties
	// ----------
	public $language = 'ru';
	public $locale = 'ru_RU.UTF8';
	public $encoding = 'UTF-8';
	public $applicationClass = 'core.sites.WebApplication';

	// Core instance is here
	// ---------------------
	public static $instance;
	public static $objects = array();

		// Start core
	// ----------
	public function start($args = array()) {

		// Set system variables
		// --------------------
		session_set_cookie_params(0);
		mb_internal_encoding($this->encoding);
		setlocale(LC_TIME, $this->locale);

		// Load core
		// ---------
		$this->load();

		$eventsModule = \Core::getModule('events');

		// Refresh
		// -------
		$eventsModule->send('coreStart');

		// Get application class
		// ---------------------
		if(!empty($this->applicationClass)) {

			$applicationClass = \Core::getComponent($this->applicationClass);

			// If application class are exists, run it
			// ---------------------------------------
			if (!empty($applicationClass)) {
				$this->application = $applicationClass::getInstance();
				$this->application->run();
			}

			// Or just error
			// -------------
			else die('Critical error. Application class '.$this->applicationClass.' doesn\'t exisits.');
		}

		// Core is finished work
		// ---------------------
		$eventsModule->send('coreStop');

		// Save unsaved objects
		// --------------------
		\Components::saveObjects();

	}

	// Save undaved objects
	// --------------------
	public function saveObjects() {
		foreach(\Core::$objects as $object) {
			if ($object->_isModified && !empty($object->_id)) {
				$object->save();
			}
		}
	}


	// Load objects
	//  -----------
	function load() {

		// Load system
		// -----------
		$this->loadConfig();
		\Loader::importPackage('core');

		// Load packages
		// -------------
		$this->loadPackages();

		// Load assets object
		// ------------------
		$this->loadAssets();
	}

	// Load system assets
	// -------------------
	function loadAssets() {

		// Init connection to assets
		// -------------------------
		$collectionClass = \Core::getComponent('core.db.Collection');
		$assetsCollection = $collectionClass::getInstance(array('id' => 'assets'));

		// Find all assets
		// ---------------
		$objects = $assetsCollection->find();
	}

	// Load configuration file
	// -----------------------
	function loadConfig() {

		// Check for config
		// ----------------
		$configFile = __DR__ . 'private/config/config.xml';

		// If config file exists
		// ---------------------
		if (file_exists($configFile)) {

			// Load file and parse
			// -------------------
			$config = simplexml_load_file($configFile);
			$config = json_decode(json_encode($config), true);

			// Set properies to core instance
			// ------------------------------
			if (!empty($config['enableSuperAccess'])) $config['enableSuperAccess'] = filter_var($config['enableSuperAccess'], FILTER_VALIDATE_BOOLEAN);
			$this->setProperties($config);

			// Main settings
			// -------------
			define('__SITE_KEY__', @ $config['siteKey']);
			define('__SITE_ID__', @ $config['siteID']);

			// Set property to install
			// -----------------------
			$this->isInstalled = true;
		}

	}

	// Load packages
	// -------------
	function loadPackages()  {

		// Packages
		// --------
		$collectionClass = \Core::getComponent('core.db.Collection');
		$packagesCollection = $collectionClass::getInstance(array('id' => 'packages'));

		// Get all active packages
		$packages = $packagesCollection->find(array('enabled' => true));

		// Load active packages
		// --------------------
		foreach($packages as $package) $package->load();

		// Parse XML
		// ---------
		$this->initXML();

	}

	// Get asset by id
	// ---------------
	public static function getAsset($id) {
		return @ \Components::$assets[$id];
	}

	// Init objects from XML
	// ---------------------
	public function initXML() {

		// Get parser
		// ----------	
		$xmlParserClass = \Core::getComponent('core.XMLParser');
		$xmlParser= $xmlParserClass::getInstance();

		// Parse all xml documents, loaded by Loader
		// -----------------------------------------
		$xmlFiles = \Loader::$files['xml'];
		foreach($xmlFiles as $file) {
			$xmlDocument = simplexml_load_file(__DR__.$file);
			$xmlParser->parseElement($xmlDocument[0]);			
		};
	}


	// Get component by type and ID
	// ----------------------------
	public static function getComponent($a, $b = null) {
		if (!empty($b)) return @ \Components::$types[$a][$b];
		return @ \Components::$components[$a];
	}

	// Get module by ID
	// ----------------
	public static function getModule($id = null) {
		return @ \Core::$instance->modules[$id];
	}

	// Get core instance
	// -----------------
	public static function getInstance($args = array()) {
		return static::$instance;
	}

	// Consturtor
	// ----------
	function __construct() {
		\Core::$instance = $this;
	}
}
