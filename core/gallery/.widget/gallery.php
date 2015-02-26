<?php

namespace Widgets;

class Gallery extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'type' => 'widget',
		'id' => 'gallery',
		'title' => 'Галерея',
		'editable' => true,
		'category' => 'gallery',
	);

	// Args format
	// -----------
	public static $widgetArgsFormat = array(

		// Large image options
		// --------------------
		'full' => array('type' => 'record', 'title' => 'Параметры большого изобравжения', 'format' => array(
			'width' => array('type' => 'number' , 'title' => 'Ширина'),
			'height' => array('type' => 'number' , 'title' => 'Высота'),
			'mode' => array('type' => 'select' , 'title' => 'Режим отображения', 'allowEmpty' => true, 'values' => array('cover' => 'Разместить с обрезкой', 'contain' => 'Разместить без обрезки')),
		)),

		// Preview image options
		//  --------------------
		'preview' => array('type' => 'record', 'title' => 'Параметры маленького изобравжения', 'format' => array(
			'width' => array('type' => 'number' , 'title' => 'Ширина'),
			'height' => array('type' => 'number' , 'title' => 'Высота'),
			'mode' => array('type' => 'select' , 'title' => 'Режим отображения', 'allowEmpty' => true, 'values' => array('cover' => 'Разместить с обрезкой', 'contain' => 'Разместить без обрезки')),
		)),

		// Gallery elements
		// ----------------
		'elements' => array('type' => 'list', 'title' => 'Изображения для вывода', 'format' => array(
			'type' => 'media', 'mediaType' => 'image'
		))
	);

	// Render single element
	// ---------------------
	public function renderElement($element) {

		// Get image
		// ---------
		$mediaClass = \Core::getClass('media');
		$media = $mediaClass::findPK(@ $element);
		if (empty($media)) return '';

		// Get images
		// ----------
		$fullMediaURI = $media->set(array('effects' => array(array('type' => 'resize', 'width' => @ $this->args['full']['width'], 'height' => @ $this->args['full']['height'], 'scaleMode' => @ $this->args['full']['mode']))))->getURI();
		$previewMedia = $media->set(array('effects' => array(array('type' => 'resize', 'width' => @ $this->args['preview']['width'], 'height' => @ $this->args['preview']['height'], 'scaleMode' => @ $this->args['preview']['mode']))));

		// Return gallery content
		// ----------------------
		@$content .= '<a class="gallery-item" href="'.$fullMediaURI.'" rel="lightbox['.$this->galleryID.']">'.$previewMedia->renderMedia().'</a>';

		return $content;


	}

	// Render
	// ------
	public function render() {

		// Elements must be places here
		// ----------------------------
		if (empty($this->args['elements'])) { return false; }

		// Set gallery ID
		// --------------
		$this->galleryID = first_var(@ $this->args['galleryID'], 'box'.\rand(0, 3000000));

		// Render elements
		// ---------------
		$content = '';
		foreach($this->args['elements'] as $element) {
			$content .= @ $this->renderElement($element);
		}

		// Return content
		// --------------
		return $content;

	}
}