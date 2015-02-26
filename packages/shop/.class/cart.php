<?php

namespace Shop\Classes;

class Cart extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'cart',
		'title' => 'Корзина',
		'editable' => true,
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'client' => array('type' => 'object', 'class' => 'client', 'title' => 'Клиент', 'listing' => true, 'sortable' => true, 'searchable' => true),
        'product' => array('type' => 'object', 'class' => 'node', 'title' => 'Продукт/Услуга'),
        'count' => array('type' => 'number', 'title' => 'Количество'),
        'date' => array('type' => 'datetime', 'title' => 'Время заказа', 'listing' => true, 'sortable' => true, 'searchable' => true),
        'confirmed' => array('type' => 'boolean', 'title' => 'Подтверждён'),
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
