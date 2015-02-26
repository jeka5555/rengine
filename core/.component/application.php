<?php

namespace Core\Sites\Components;

class Application extends \Component {

	// Init component
	// --------------
	public static $component = array(
		'id' => 'application',
		'type' => 'component',
		'autoload' => true,
		'title' => 'Приложение ядра'
	);


	// Create application
	// ------------------
	public static function getInstance($args = null) {

		// Create application
		// ------------------
		$application = parent::getInstance($args);

		// Register event listener
		// -----------------------
		\Events::addListener(null, $application);

		return $application;
	}

	// Run application
	// ---------------
	public function runApplication() {}

}