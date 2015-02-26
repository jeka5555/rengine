<?php

namespace Robokassa\Classes;

class Orders extends \ObjectClass {

	public static $component = array(
		'id' => 'orders',
		'editable' => true,
		'title' => 'Заказы',
        'editable' => true,
	);


    // Class properties
    // ----------------
    public static $classProperties = array(
        'name' => array('id' => '', 'type' => 'text', 'title' => 'Имя', 'listing' => true, 'sortable' => true),
        'phone' => array('id' => '', 'type' => 'text', 'title' => 'Телефон', 'listing' => true, 'sortable' => true),
        'address' => array('id' => '', 'type' => 'textarea', 'title' => 'Адрес доставки'),
        'summ' => array('id' => '', 'type' => 'text', 'title' => 'Сумма заказа', 'listing' => true, 'sortable' => true),
        'count' => array('id' => '', 'type' => 'text', 'title' => 'Количество', 'listing' => true, 'sortable' => true),
        'isPaid' => array('id' => 'isPaid', 'type' => 'boolean', 'title' => 'Оплачен', 'listing' => true, 'sortable' => true),
        'date' => array('id' => 'date', 'type' => 'datetime', 'title' => 'Время заказа', 'listing' => true, 'sortable' => true)
    );
}