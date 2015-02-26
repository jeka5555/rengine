<?php

class Packages {

	// Каталог модулей
	// ---------------
	public static $packages = array();

	// Получить модуль
	// ---------------
	public static function get($id) {
		return @ static::$packages[$id];
	}	

	// Load
	// ----
	public static function load($args = array()) {

		// Загрузка модуля как строки
		// --------------------------
		if (is_string($args)) {
			\Loader::importPackage($args);
			\Logs::log('Загрузка пакета '.$args, 'debug');
			return true;
		}
	}

	// Load all packages of site
	// -------------------------
	public static function loadList($args = array()) {
		foreach($args as $package) self::load($package);
	}

}