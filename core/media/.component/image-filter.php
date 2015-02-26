<?php

namespace Core\Components;

class ImageFilter extends \Core\Media\Classes\Media {

	// Init component
	// --------------
	public static $component = array(
		'id' => 'image-filter',
		'type' => 'component',
		'autoload' => true,
		'title' => 'Фильтр изображеений'
	);

	// Process
	// -------
	public static function process($resource = null, $settings = array()) {
		return $resource;
	}
}
