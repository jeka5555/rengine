<?php


namespace Core\ImageFilters;


class Composite extends \Core\Components\ImageFilter {

	// Component
	// ---------
	public static $component = array(
		'id' => 'composite',
		'title' => 'Эффект modulate'
	);

	// Эффект наложение изображения
	// --------------
	public static function process($resource = null, $args = array()) {
	
		if(empty($resource)) return false;

		if($args['position'] == 'center') {
			$args['x'] = ($this->getWidth() / 2) - ($args['composite_object']->imageResource->getImageWidth() / 2);
			$args['y'] = ($this->getHeight() / 2) - ($args['composite_object']->imageResource->getImageHeight() / 2);
		}
		$resource->compositeImage($args['composite_object']->imageResource, $args['composite_object']->imageResource->getImageCompose(),  $args['x'],  $args['y']);

		
		return $resource;
	}
	
}