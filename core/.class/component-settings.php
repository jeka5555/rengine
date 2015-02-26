<?php
namespace Core\Classes;

class ComponentSettings extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'id' => 'component-settings',
		'title' => 'Настройки компонентов'
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'type' => array('title' => 'Тип компонента', 'type' => 'text', 'listing' => true, 'sortable' => true),
		'id' => array('title' => 'ID компонента', 'type' => 'text',  'listing' => true, 'sortable' => true),
		'data'=> array('title' => 'Данные', 'type' => 'record'),
	);

}
