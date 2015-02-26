<?php

namespace Geo;

class District extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'geo-distric',
		'title' => 'География.Район города',
		'editable' => true
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'title' => array('type' => 'text', 'title' => 'Название города', 'listing' => true, 'sortable' => true),
		'city' => array('type' => 'object', 'class' => 'geo-city', 'title' => 'Город',  'listing' => true, 'sortable' => true),
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'read' => array(array('type' => 'true'))
	);

}
