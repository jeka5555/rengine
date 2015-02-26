<?php

namespace Geo;

class Country extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'geo-country',
		'title' => 'География.Страна'
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'title' => array('id' => 'title', 'type' => 'text', 'title' => 'Название',  'listing' => true, 'sortable' => true)
	);

	// Access
	// ------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'read' => array(array('type' => 'true'))
	);


}
