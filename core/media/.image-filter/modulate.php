<?php


namespace Core\ImageFilters;


class Modulate extends \Core\Components\ImageFilter {

	// Component
	// ---------
	public static $component = array(
		'id' => 'modulate',
		'title' => 'Эффект modulate'
	);

	// Process filter
	// --------------
	public static function process($resource = null, $args = array()) {
	
		if(empty($resource)) return false;

		$resource->modulateImage($args['brightness'], $args['saturation'], $args['hue']);
		
		return $resource;
	}
	
}