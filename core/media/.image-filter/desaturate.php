<?php


namespace Core\ImageFilters;


class Desaturate extends \Core\Components\ImageFilter {

	// Component
	// ---------
	public static $component = array(
		'id' => 'desaturate',
		'title' => 'Эффект desaturate (обесцветить)'
	);

	// Process filter
	// --------------
	public static function process($resource = null, $args = array()) {
		
		if(empty($resource)) return false;
	
		$resource->modulateImage(100, 0, 100);  
		
		return $resource;
	}
	
}