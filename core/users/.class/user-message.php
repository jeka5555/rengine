<?php
namespace Users\Classes;

class UserMessage extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'id' => 'user-message',
		'title' => 'Сообщение пользователя'
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'user' => array('title' => 'Получатель сообщения', 'type' => 'object', 'class' => 'user', 'listing' => true, 'sortable' => true),
		'text' => array('title' => 'Текст сообщения', 'type' => 'text'),
		'from' => array('title' => 'Отправитель', 'type' => 'object', 'class' => 'user', 'listing' => true, 'sortable' => true),
		'isRead' => array('title' => 'Сообщение прочитано', 'type' => 'boolean', 'listing' => true, 'sortable' => true),
		'time' => array('title' => 'Время отправки', 'type' => 'datetime', 'listing' => true, 'sortable' => true)
	);

}
