<?php

namespace Core\Admin\Components;

class AdminApplication extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'admin-application',
		'autoload' => true,
		'title' => 'Приложение администрирования'
	);


	// Get execute command
	// -------------------
	public static function getExecuteCommand() {}

	// Run command
	// -----------
	public function command($command, $args = array()) {

		// Execute and return result
		// -------------------------
		if (method_exists($this, 'command'.$command)) $result = call_user_func(array($this, 'command'. $command), $args);
		else $result = null;
		return $result;
	}

	// Load components for module
	// --------------------------
	public static function loadComponentsDirectory($args = array()) {

		// Found all applications
		// ----------------------
		$dirs = glob( $args['path'] . "/*", GLOB_ONLYDIR);

		// Load each as module
		// -------------------
		foreach($dirs as $directory) {
			\Loader::importObject(array('as' => 'module', 'path' => $directory, 'id' => substr($directory, strlen($args['path']))));
		}

	}

}
