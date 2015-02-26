<?php

namespace Core\Components;

class Assets extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'assets',
		'autoload' => true,
		'title' => 'Ресурсы для генерации страницы'
	);

	// Load components for module
	// --------------------------
	public static function loadComponentsDirectory($args = array()) {

		// Import subdirs
		// --------------
		\Loader::importObject(array('as' => 'directory', 'path' => $args['path']));
		\Loader::importObject(array('as' => 'directory', 'path' => $args['path'].'/css'));
		\Loader::importObject(array('as' => 'directory', 'path' => $args['path'].'/js'));

	}

}
