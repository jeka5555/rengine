<?php

namespace Core\Objects;

class Template extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'id' => 'template',
		'title' => 'Шаблон',
		'editable' => true
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'or', 'rules' => array(
			array('type' => 'userRole', 'role' => 'administrator'),
			array('type' => 'userRole', 'role' => 'developer')
		))),
		'read' => array(array('type' => 'true'))
	);

	// Class properties
	// ----------------
	public static $classProperties = array(

		// Description
		// -----------
		'id' => array('type' => 'text', 'title' => 'Идентификатор', 'listing' => true, 'sortable' => true),
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true),
		'description' => array('type' => 'text', 'title' => 'Описание', 'input' => 'textarea'),

		// Main properties
		// ---------------
		'class' => array('type' => 'select', 'title' => 'Класс шаблона', 'componentType' => 'widget', 'listing' => true, 'sortable' => true, 'allowEmpty' => true, 'values' => array(
			'page' => 'Страница', 'widget' => 'Виджет'
		)),
		'engine' => array('type' => 'component', 'title' => 'Движок шаблонизации', 'listing' => true, 'sortable' => true, 'componentType' => 'templateEngine'),
		'sourceFile' => array('type' => 'text', 'title' => 'Файл исходника'),

		// Source
		// ------
		'source' => array('type' => 'text', 'title' => 'Исходный код', 'input' => 'textarea', 'isHTML' => true)

	);
}