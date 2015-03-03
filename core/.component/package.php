<?php

class Package extends \Component {

	// Init component
	// --------------
	public static $component = array(
		'id' => 'package',
		'type' => 'component',
		'autoload' => true,
		'title' => 'Пакет расширений'
	);


	// Create application
	// ------------------
	public static function getInstance($args = null) {

		// Create application
		// ------------------
		$package = parent::getInstance($args);
		return $package;
	}

	// On enable
	// ------------
	public static function enablePackage() {
		$packageClass = \Core::getClass('package');
		$package = $packageClass::findOne(array('query' => array('id' => static::$component['id'])));
		$package->enable = true;
		$package->save();
	}

	// On disable
	// ------------
	public static function disablePackage() {
		$packageClass = \Core::getClass('package');
		$package = $packageClass::findOne(array('query' => array('id' => static::$component['id'])));
		$package->enable = false;
		$package->save();
	}	

	public static function installPackage() {
		$packageClass = \Core::getClass('package');
		$package = $packageClass::getInstance(array('id' => static::$component['id']));
		$package->save();	
	}

	// On uninstall
	// ------------
	public static function uninstallPackage() {

		// Get package component
		// ---------------------
		$packageComponent = \Core::getComponent('package', static::$component['id']); 
		if (!empty($packageComponent))
			$packageComponent::disablePackage();

		// Delete object
		// -------------
		$packageClass = \Core::getClass('package');
		$package = $packageClass::findOne(array('query' => array('id' => static::$component['id'])));
		if (!empty($package))
			$package->delete();

		// Delete package dir
		// ------------------
		if (is_dir(__DR__ . 'packages/' . static::$component['id'])) {
			self::rrmdir(__DR__.'packages/'.static::$component['id']);
		}
	
		return true;
	}
	
	// recursively remove a directory
	// -------------
	public static function rrmdir($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
		  (is_dir($dir.'/'.$file)) ? self::rrmdir($dir.'/'.$file) : unlink($dir.'/'.$file);
		}
		return rmdir($dir);
	} 

	

}