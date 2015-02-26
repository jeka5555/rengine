<?php

namespace Modules;

class TwigTemplates extends \Module {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'twig',
		'title' => 'Шаблонизатор Twig',
		'version' => 0.4,
	);

	// Register component
	// ------------------
	public static function registerComponent() {

		parent::registerComponent();

		// Load data
		// ---------
		$componentPath = static::$component['subpackagePath'];
		require_once(__DR__.$componentPath.'/.lib/Twig/Autoloader.php');
		\Twig_Autoloader::register();


	}

}
