<?php

namespace Users\Classes;

class UserGroup extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'userGroup',
		'title' => 'Группа пользователей',
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
		'title' => array( 'type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true),
		'description' => array('type' => 'text', 'title' => 'Описание', 'input' => 'textarea'),
		'users' => array('type' => 'list', 'title' => 'Пользователи в группе', 'format' => array('type' => 'object', 'class' => 'user'))
	);

	// Get identity title
	// ------------------
	public function getIndentity() {
		return first_var(@ $this->title, @ $this->id);
	}
}