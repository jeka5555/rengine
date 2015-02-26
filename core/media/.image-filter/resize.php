<?php


namespace Core\ImageFilters;


class Resize extends \Core\Components\ImageFilter {

	// Component
	// ---------
	public static $component = array(
		'id' => 'resize',
		'title' => 'Эффект ресайза'
	);

	// Process filter
	// --------------
	public static function process($resource = null, $args = array()) {
	
		if(empty($resource)) return false;

		// Если не указана ширина или высота, то берём у оригинала
		if(empty($args['width'])) $args['width'] = $resource->getImageWidth();
		if(empty($args['height'])) $args['height'] = $resource->getImageHeight();

	  	// Определение режима
		$scaleMode = first_var(@ $args['scaleMode'], 'contain');

		if($scaleMode == 'contain') {
			if($args['width'] > $resource->getImageWidth() and $args['height'] > $resource->getImageHeight()) return $resource;
			$resource->thumbnailImage($args['width'], $args['height'], true, false);
		} else
			$resource->cropThumbnailImage($args['width'], $args['height']); 
			
		return $resource;
	}
	
}