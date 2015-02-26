<?php     

// Расширение
// ----------
class Component extends CoreObject {

	// Информация компонента
	// ---------------------
	public static $component = array(
		'type' => 'component',
		'id' => 'component',
		'title' => 'Компонент Rengine'
	);
	

	public static $componentTranslationTable;
	public static $componentTypes = array();
	public static $settings = array();
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
		if (@ static::$component['initialized'] == true) return; 

		// Apply settings
		// --------------
		if (@ static::$component['hasSettings'] == true) {}

		// Set initialized flag
		// --------------------
		static::$component['initialized'] = true; 


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

	// Register component in the system. Only load time, no runtime
	// ------------------------------------------------------------
	public static function registerComponent() {

		$class = get_called_class();

		// Skip registred component
		// ------------------------
		if (@ $class::$component['isRegistred'] == true) return;

		$reflectionClass = new ReflectionClass($class);

		// Get component declaration
		// -------------------------
		$componentProperty = $reflectionClass->getProperty('component');
		$componentValue = $componentProperty->getValue();
		$componentClass = $componentProperty->getDeclaringClass();

		// If not declaration, copy from parent
		// ------------------------------------
		if ($reflectionClass != $componentClass) {
			$reflectionClass->setStaticPropertyValue('component', array());
		}
	
		// Set flags
		// ---------
		$class::$component['class'] = $class;
		$class::$component['isComponent'] = true;
		$class::$component['isRegistred'] = true;

		// Autodetect type
		// ---------------
		if (empty($class::$component['type'])) {

			// Get parent
			// ----------
			$cls = $reflectionClass->getParentClass();

			// If parent is set
			// ----------------
			while(!empty($cls)) {

				// Read component property
				// -----------------------
				$componentData = $cls->getStaticPropertyValue('component');

				if (empty($componentData)) break;

				// If component has component data
				// -------------------------------
				if (is_array($componentData)) {

					// If this is root component. apply it's id as type
					// ------------------------------------------------
					if (($cls->name == 'Component' || @ $componentData['type'] == 'component' ) && isset($componentData['id'])) {
						$class::$component['type'] = $componentData['id'];
						break;
					}

					// Else, look for type
					// -------------------
					if (!empty($componentData['type'])) {
						$class::$component['type'] = $componentData['type'];
						break;
					}

				} 

				// Next class
				// ----------							
				$cls = $cls->getParentClass();

			} 

			// Set as component, if not found
			// ------------------------------
			if (empty($class::$component['type'])) $class::$component['type'] = 'component';
		}
					
		// Create unique id
		// ----------------
		if (empty($class::$component['id'])) {
			$name = get_called_class();
			$name = str_replace('\\', '-', $name);
			if ($name[0] == '-') $name = substr($name, 1);
			$class::$component['id'] = strtolower($name);
		}	

		// If this is component type
		// --------------------------
		if ($class::$component['type'] == 'component') {
		
			// Add to component types
			// ----------------------
			self::$componentTypes[$class::$component['id']] = $class;

			// If we use autoload, add it to loader's list
			// -------------------------------------------
			if (@$class::$component['autoload'] == true) {
				\Loader::$types[] = $class::$component['id'];
			}
		}
	
		// Get settings from parent
		// ------------------------
		$parentClass = get_parent_class($class);

		$parentHasData = !empty($parentClass::$component);
		$parentIsComponent = is_subclass_of($parentClass,'\Component');

		if ($parentIsComponent && $parentHasData) {
			$hasTitle = !empty($class::$component['title']);
			$class::$component = array_merge($parentClass::$component, $class::$component);
			if (!$hasTitle) $class::$component['title'] = null;
		}

		// Add component to extensions
		// ---------------------------
		\Extension::add(static::$component);

		// Apply settings
		// --------------
		if (@ $class::$component['hasSettings'] == true) {
			$settingsClass = \Core::getClass('component-settings');

			if (!empty($settingsClass)) {

				// Read settings
				// -------------
				$settings = $settingsClass::findOne(array('query' => array(
					'type' => $class::$component['type'],
					'id' => $class::$component['id']
				)));

				// Apply settings
				// --------------
				if (!empty($settings)) {					
					$settings = array_merge($class::$settings, $settings->data);
					$reflectionClass->setStaticPropertyValue('settings', $settings);
				}


			}
		}


		// Import resources
		// ----------------
		if (!empty($class::$componentImports)) {
			foreach($class::$componentImports as $import) {

				// Single resource file
				// --------------------
				if (is_string($import)) {
					$path = $class::$component['componentPath'].'/'.$import;
					\Loader::importObject(array('as' => 'file', 'path' => $path));
				}

			}		
		}

	}


	// Методы для установки свойств
	// ----------------------------
	public function set($args = array(), $value = null)	{

		// Для обычного свойства
		// ---------------------
		if (!is_array($args)) {
			$this->__set($args, $value);
		}

		// Установка массива
		// -----------------
		else {
			foreach ($args as $variable => $value) {
				$this->__set($variable, $value);
			}
		}

		return $this;

	}

	// Get property
	// ------------
	public function getProperty($prop = null)	{

		// Return via getter method
		// ------------------------
		$getterMethod = 'get'.$prop;
		if (method_exists($this, $getterMethod)) {
			return call_user_func(array($this, $getterMethod));
		}

		// Or from properties
		// ------------------
		return @ $this->properties[$prop];

	}



	// Translate
	// ---------
	public function translate($text, $options = array()) {

		// Connect to translation module
		// -----------------------------
		$translationModule = \Core::getModule('translate');
		if (empty($translationModule)) return $text;

		return $translationModule->translate($text, $options);

	}

	// Translate local
	// ---------------
	public function localTranslate($text, $options = array()) {		
		$options['namespace'] = static::$component['type'].'-'.static::$component['id'];
		return $this->translate($text, $options);
	}


	// Load components for module
	// --------------------------
	public static function loadComponentsDirectory($args = array()) {}


	// Get list of components
	// ----------------------
	public static function getComponentsList() {

		$type = static::$component['id'];

		$result = array();

		if (!empty(\Extension::$ext[$type])) {
			foreach(\Extension::$ext[$type] as $componentID => $component) {
					$componentClass = $component['class'];
					$result[$componentID] = first_var(@ $componentClass::$component['title'], $componentClass::$component['id']);
			}
		}

		return $result;
	}

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
