<?php

// Класс для работы с расширениями
// --------------------------------------
class Extension {

	// Здесь храним расширения
	static $ext = array(); 
	static $initComponentIndex = 0;

	// Инициализация подключенных компонентов
	// --------------------------------------
	public static function initComponents() {

		// Очередь инициализации компонентов
		// ---------------------------------
		$order = array();

		foreach(\Extension::$ext['all'] as & $comp) {

			if (@ $comp['isInitialized'] == true) continue;
		
			$initOrder = first_var(@ $comp['initOrder'], 0);
		
			$order[$initOrder][] = & $comp;
		}		

		// Инициализация по очереди
		// ------------------------
		foreach($order as & $list) {
			if (!empty($list)) {
				foreach($list as & $component) {

						// Если уже инициализирован, пропускаем
						// ------------------------------------
						if (@ $component['isInitialized'] == true) continue;

						// Считываем класс
						// ---------------
						$class = @ $component['class'];

						// Инициализация
						// -------------
						if (!empty($class) && is_subclass_of($class, '\Component')) {
							$class::initComponent();
						}
						$component['isInitialized'] = true;
				}
			}			
		}

	}


	// Регистрация расширения
	// ----------------------------------
	public static function addComponent($className) {
	
		if (class_exists($className)) {
			$className::$component['packagePath'] = end(\Loader::$packagePath);
			$className::$component['subpackagePath'] = end(\Loader::$subpackagePath);
			$className::$component['componentPath'] = end(\Loader::$componentPath);
			$className::$component['componentFile'] = end(\Loader::$componentFiles);
			call_user_func(array($className, 'registerComponent'));	
		}
	}


	// Регистрация расширения
	// ----------------------------------
	public static function add($extension = array()) {
	
		// Если нет типа, то создаем
		// ------------------------------
		if (!isset(self::$ext[$extension['type']])) {
			self::$ext[$extension['type']] = array();
		}	

		// Добавляем расширение в тип
		// ------------------------------
		self::$ext[$extension['type']][$extension['id']] = & $extension;
		self::$ext['all'][] = & $extension;

	}


}