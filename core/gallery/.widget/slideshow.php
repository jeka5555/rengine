<?php

namespace Widgets;

class Slideshow extends \Widget {

	// Информация о компоненте
	// -----------------------
	public static $component = array(
		'type' => 'widget',
		'id' => 'slideshow',
		'title' => 'Слайдшоу',
		'editable' => true,
		'category' => 'gallery'
	);
	
	public static $widgetArgsFormat = array(
		'width' => array('type' => 'number', 'title' => 'Ширина'),
		'height' => array('type' => 'number', 'title' => 'Высота'),
		'mode' => array('type' => 'select', 'title' => 'Режим отображения', 'allowEmpty' => true, 'values' => array('cover' => 'Разместить с обрезкой', 'contain' => 'Разместить без обрезки')),
		'images' => array('type' => 'list', 'title' => 'Изображения для вывода', 'format' => array('type' => 'media', 'mediaType' => 'image'), 'folderPath' => array('Слайдшоу'))	
	);

	// Отображение
	// -----------
	public function render() {

		// Результат вывода тут
		// --------------------
		$result = array();

		// Считываем данные по медиа
		// -------------------------
		if (empty($this->args['images'])) { return false; }

		// Получаем изображения
		// -------------------------
		foreach($this->args['images'] as $image) {
			@$elementsContent .= \Widgets::get('media', array('mediaID' => @$image,  'width' => @$this->args['width'], 'height' => @$this->args['height'], 'mode' => @$this->args['mode']), array('tag' => false));
		}
		
		$content = '<div
			class="cycle-slideshow"
			data-cycle-auto-height="container">'.$elementsContent.'</div>';

		return $content;

	}
}