<?php

namespace Geo;

class City extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'geo-city',
		'title' => 'География.Город',
		'editable' => true
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'title' => array('id' => 'title', 'type' => 'text', 'title' => 'Название города', 'listing' => true, 'sortable' => true),
		'region' => array('id' => 'region', 'type' => 'object', 'class' => 'geo-region', 'title' => 'Регион', 'listing' => true, 'sortable' => true),
		'alias' => array('id' => 'alias', 'type' => 'text', 'title' => 'Сокращенное название', 'listing' => true, 'sortable' => true),
		'order' => array('id' => 'order', 'type' => 'text', 'title' => 'Сортировка', 'listing' => true, 'sortable' => true)
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'read' => array(array('type' => 'true'))
	);

}
