<?php

namespace Packages\Classes;

class Package extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'package',
		'title' => 'Пакет',
		'editable' => true
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
	 	'id' => array('type' => 'text', 'title' => 'ID пакета', 'listing' => true, 'sortable' => true),
	 	'enable' => array('type' => 'boolean', 'title' => 'Включен', 'listing' => true, 'sortable' => true),
	 	'order' => array('type' => 'text', 'title' => 'Порядок загрузки', 'listing' => true, 'sortable' => true)
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'read' => true
	);

}
