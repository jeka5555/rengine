<?php

namespace Shop\Classes;

class Attribute extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'attribute',
		'title' => 'Магазин.Аттрибут',
		'editable' => true,
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'text' => array('type' => 'textarea', 'isHTML' => true, 'title' => 'Описание', 'searchable' => true),
		'order' => array('type' => 'number', 'title' => 'Порядок вывода', 'listing' => true, 'sortable' => true),
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
