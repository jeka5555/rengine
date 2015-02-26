<?php

namespace Core\Events\Classes;

class Event extends \ObjectClass {

	// Компонент
	// ---------
	public static $component = array(
		'id' => 'event',
		'title' => 'Событие'
	);

	// Properties
	// ----------
	public static $classProperties = array(
		'user' => array('type' => 'object', 'class' => 'user', 'title' => 'Пользователь', 'listing' => true, 'sortable' => true),
		'event' => array('type' => 'text', 'title' => 'Тип события', 'listing' => true, 'sortable' => true),
		'data' => array('type' => 'record', 'title' => 'Данные события'),
		'visibility' => array('type' => 'record', 'title' => 'Видимость события')
	);

}
