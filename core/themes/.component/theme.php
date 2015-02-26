<?php

class Theme extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'theme',
		'title' => 'Тема оформления',
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

	// Register all theme components
	// -----------------------------
	public static function registerComponent() {
		parent::registerComponent();
	}

	// Get list of all theme templates
	// -------------------------------
	public function getTemplatesList() {
	}

	// Get list of all theme widgts
	// ----------------------------
	public function getWidgetsList() {

	}
}
