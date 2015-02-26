<?php

namespace Users\Classes;

class UserPermission extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'userPermission',
		'title' => 'Привелегия пользователя',
		'editable' => true
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'get' => array(array('type' => 'true')),
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'id' => array('type' => 'text', 'title' => 'Идентификатор', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'description' => array('type' => 'text', 'title' => 'Краткое описание', 'input' => 'textarea', 'listing' => true, 'sortable' => true, 'searchable' => true)
	);
}