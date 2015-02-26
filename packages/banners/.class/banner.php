<?php

namespace Banners\Classes;

class Banner extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'banner',
		'title' => 'Баннер',
		'editable' => true,
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true, 'searchable' => true),

		// Data
		// ----
		'image' => array('type' => 'media','title' => 'Изображение баннера', 'mediaType' => 'image', 'folderPath' => array('Баннеры')),
		'text' => array('type' => 'text', 'title' => 'Текст баннера', 'type' => 'textarea', 'isHTML' => true),

		// Output
		// ------
		'link' => array('type' => 'text', 'title' => 'Ссылка для перехода'),
		'htmlClass' => array('type' => 'text', 'title' => 'Дополнительный HTML-класс'),
		'group' => array('type' => 'text', 'title' => 'Группа', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'hidden' => array('type' => 'boolean', 'title' => 'Скрыт', 'listing' => true, 'sortable' => true),
		'priority' => array('type' => 'number', 'precision' => 'integer', 'title' => 'Приоритет вывода')
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(
			array('type' => 'or', 'rules' => array(
				array('type' => 'userRole', 'role' => 'super'),
				array('type' => 'userRole', 'role' => 'administrator'),
				array('type' => 'userRole', 'role' => 'content-manager')
			))
		),
		'read' => true
	);
	
}
