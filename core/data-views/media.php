<?php

namespace Core\DataViews;

// Изображение
// ------------------
class Media extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'media'
	);

	// Отображение
	// -----------
	public function execute() {

		$data = array(
			'mediaID' => $this->value,
			'width' => @ $this->options['width'],
			'height' => @ $this->options['height'],
			'mode' => @ $this->options['mode'],
			'filters' => @ $this->options['filters'],
            'title' => @ $this->options['title'],
		);

		// If we need image link
		// ---------------------
		if(@ $this->options['getLink']) {
			$mediaClass = \Core::getClass('media');
			$media = $mediaClass::findPK($this->value);
			if(!empty($media)) {
				$media->width = @ $this->options['width'];
				$media->height = @ $this->options['height'];
				$media->mode = @ $this->options['mode'];
				return $media->getURI();
			} else
				return false;
		}

		// Or just media
		// -------------
		$widgetComponent = \Core::getComponent('widget', 'media');
		$widget = $widgetComponent::getInstance(array('args' => $data));
		return $widget->render();

	}
}
