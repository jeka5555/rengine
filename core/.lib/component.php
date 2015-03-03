<?php



// Core component
// --------------
class Component extends CoreObject {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'component',
		'title' => 'Компонент Rengine'
	);

	public static $settings = array(); // Component settings
	public $guid; // Unique identifier of component

	// Get format of form
	// ------------------
	public function getComponentSettingsFormat() {
		if (isset(static::$componentSettingsFormat)) return static::$componentSettingsFormat;
		else return array();	
	}

	// Get component settings
	// ----------------------
	public function getComponentSettings($settingVariable = null) {

		// Get settings
		// ------------
		$settings = first_var(@ static::$settings, array());
		if (empty($settingVariable)) return $settings;

		// Or return single one
		// --------------------
		return @ $settings[$settingVariable];
	}


	// Get component editor format
	// ---------------------------
	public function getComponentEditorStructure() {}


	// Init component, all system modules are loaded
	// ---------------------------------------------
	public static function initComponent() {

		// Already initialized?
		// --------------------
		if (@ static::$component['isInitialized'] == true) return;

		// Set initialized flag
		// --------------------
		static::$component['isInitialized'] = true;

		$eventsModule = \Core::getModule('events');
		if (!empty($eventsModule)) {
			\Events::send('componentInitialized', get_called_class());
		}

	}

	// Apply component settings
	// ------------------------
	public static function applyComponentSettings($settings = array()) {
		static::$component['settings'] = array_merge(static::$component['settings'], $settings);
	}


	// Load components for module
	// --------------------------
	public static function loadComponentsDirectory($args = array()) {}


	// Route event
	// -----------
	public function dispatchEvent($eventType, $data = null) {

		$methodName = 'event'.$eventType;

		// If event method found, process it
		// ---------------------------------
		if (method_exists($this, $methodName)) {
			call_user_func(array($this, $methodName), $eventType, $data);
		}

	}


}
