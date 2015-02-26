<?php

namespace Core\DataViews;

class MediaURI extends \Component {

	// Component definition
	// --------------------
	public static $component = array(
		'type' => 'dataView',
		'id' => 'media-uri'
	);

	// Отображение
	// -----------
	public function execute($args = array()) {

		// Читаем медиа
		// ------------
		$mediaClass = \Core::getClass('media');
		$media = $mediaClass::findPK($this->value);

		// Отдаем ссылку
		// -------------
		if(!empty($media)) {
			$media->width = @ $this->options['width'];
			$media->height = @ $this->options['height'];
			$media->mode = @ $this->options['mode'];
			return $media->getURI();
		}
		else return '#';
	}
}
