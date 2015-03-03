<?php   

class CoreObject {

	public static $component = array();
	public $properties = array();

	// Фабрика
	// -------
	public static function getInstance($args = array()) {
		$object = new static($args);
		return $object;
	}

	// Создание объекта
	// ----------------
	public function __construct($args = null) {    

		// Set variables
		// -------------
		if (is_array($args)) {
			foreach($args as $var => $value) $this->$var = $value;
		}

		// From existing object
		// --------------------
		else if (is_subclass_of($args, 'CoreObject')) {
			$this->properties = $args->properties;
		}
	}

	// Проверка наличия свойства
	// -------------------------
	public function __isset($var) {
		if (isset($this->properties[$var])) return true;
		return false;
	}

	// Универсальный способ получения свойств
	// -------------------------------------
	public function & __get($var) {
		$method = 'get'.ucfirst($var);
		if (method_exists($this, $method)) {
			$result = call_user_func(array($this, $method));
			return $result;
		}
		$props = & $this->properties;	
		return $props[$var];
	}

	// Установка переменной
	// --------------------
	public function __set($var, $value = null) {

		if (is_null($value)) {
			unset($this->properties[$var]);
			return $this;
		}

		$method = 'set'.ucfirst($var);

		// Call setter
		// -----------
		if (method_exists($this, $method)) {
			call_user_func(array($this, $method), $value);
		}

		// Or simple set
		// -------------
		else {
			$this->properties[$var] = $value;
		}

		return $this;
	}

	// Get property
	// ------------
	public function getProperty($prop = null)
	{

		// Return via getter method
		// ------------------------
		$getterMethod = 'get' . $prop;
		if (method_exists($this, $getterMethod)) {
			return call_user_func(array($this, $getterMethod));
		}

		// Or from properties
		// ------------------
		return @ $this->properties[$prop];

	}

	// Setter
	// ------
	public function set($args = array(), $value = null)
	{

		// Property
		// --------
		if (!is_array($args)) $this->__set($args, $value);

		// Array property
		// --------------
		else {
			foreach ($args as $variable => $value) $this->__set($variable, $value);
		}
		return $this;

	}


}
