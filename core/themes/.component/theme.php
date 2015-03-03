<?php

class Theme extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'theme',
		'title' => 'User interface theme',
		'autoload' => true
	);

	// Load components for module
	// --------------------------
	public static function loadComponentsDirectory($args = array()) {

		// Locate submodules
		// -----------------
		$dirs = glob( $args['path'] . "/*", GLOB_ONLYDIR);

		// Load them
		// ---------
		foreach($dirs as $directory) {
			$themeID = 'theme-'.substr($directory, strlen($args['path']));
			\Loader::importObject(array('as' => 'module', 'path' => $directory, 'id' => $themeID ));
		}

	}

	// Theme properties
	// ----------------
	public $title = '';
	public $id = '';
	public $author = '';
	public $version = 1.0;
	public $description = '';
	public $options = array();
}
