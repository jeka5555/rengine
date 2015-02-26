<?php
namespace Widgets;

class Media extends \Widget {

	// Компонент
	// ---------
	public static $component = array(
		'type' => 'widget',
		'editable' => true,
		'category' => 'media',
		'title' => 'Медиа',
		'id' => 'media'
	);

	// Поля виджета
	// ------------
	public function getWidgetArgsFormat() {
		return array(
			'mediaID' => array('title' => 'Идентификатор медиа', 'type' => 'media'),
			'width' => array('title' => 'Ширина', 'type' => 'number'),
			'height' => array('title' => 'Высота', 'type' => 'number'),
			'download' => array('title' => 'Режим скачивания', 'type' => 'boolean'),
			'mode' => array('title' => 'Режим обрезки', 'type' => 'number', 'values' => array(0 => 'Обрезка', 1 => 'Заполнение')),
			'link' => array('title' => 'Дополнительная ссылка', 'type' => 'text'),
            'title' => array('title' => 'Всплывающее название', 'type' => 'text'),
		);
	}

	// Визуализация
	// ------------
	public function render() {

		// Читаем объект из базы данных
		// -------------
		if (empty($this->args['mediaID'])) return;

		$mediaClass = \Core::getComponent('class', 'media');

		// Загрузка объекта
		// ----------------
		$this->media = $mediaClass::findPK($this->args['mediaID']);
		if (empty($this->media)) return false;

		// Передаем параметры
		// ------------------
		if (!empty($this->args['mode'])) $this->media->mode = $this->args['mode'];
		if (!empty($this->args['height'])) $this->media->height = $this->args['height'];
		if (!empty($this->args['width'])) $this->media->width = $this->args['width'];
		if (!empty($this->args['getURI'])) $this->media->getURI = $this->args['getURI'];
		if (!empty($this->args['title'])) $this->media->title = $this->args['title'];

		// Выдача в вывод
		// --------------
		$result = $this->media->renderMedia();

		if (!empty($this->args['link'])) $result = '<a href="'.$this->args['link'].'">'.$result.'</a>';
		return $result;

	}

}
