<?php


namespace Core\ImageFilters;


class Blur extends \Core\Components\ImageFilter {

	// Component
	// ---------
	public static $component = array(
		'id' => 'blur',
		'title' => 'Размывание изображения'
	);

	// Process filter
	// --------------
	public static function process($resource = null, $args = array()) {
		
		if(empty($resource)) return false;
		
		$resource->blurImage($args['radius'], 3);
		
		return $resource;
	}
}