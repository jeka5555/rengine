<?php

namespace Geo;

class Region extends \ObjectClass {

	public static $component = array(
		'type' => 'class',
		'id' => 'geo-region',
		'title' => 'География.Регион'
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'read' => array(array('type' => 'true'))
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		array('id' => 'title', 'type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true),
		array('id' => 'type', 'type' => 'select', 'title' => 'Тип', 'values' => array(
			'region' => 'Область',
			'city' => 'Город',
			'republic' => 'Республика',
			'territory' => 'Край',
			'autonomous-area'  => 'Автономный округ'
		), 'listing' => true, 'sortable' => true),
		array('id' => 'country', 'type' => 'object', 'class' => 'geo-country', 'title' => 'Страна', 'listing' => true, 'sortable' => true),

		array('id' => 'alias', 'type' => 'text', 'title' => 'Сокращенное название',  'listing' => true, 'sortable' => true),
		array('id' => 'order', 'type' => 'text', 'title' => 'Сортировка'),
	);

}