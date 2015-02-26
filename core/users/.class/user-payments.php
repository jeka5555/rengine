<?php
namespace Users\Classes;

class UserPayments extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'id' => 'user-payment',
		'title' => 'Платеж пользователя'
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'type' => array('type' => 'select', 'title' => 'Тип операции', 'values' => array('income' => 'Зачисление', 'outcome' => 'Списание'), 'listing' => true, 'sortable' => true),
		'time' => array('type' => 'datetime', 'title' => 'Время', 'listing' => true, 'sortable' => true),
		'value' => array('title' => 'Сумма', 'type' => 'number', 'listing' => true, 'sortable' => true),
		'user' => array('title' => 'Пользователь', 'type' => 'object', 'class' => 'user', 'listing' => true, 'sortable' => true),
		'desription' => array('type' => 'text', 'title' => 'Описание'),
		'status' => array('type' => 'select', 'title' => 'Статус', 'sortable' => true, 'values' => array(
			'waiting' => 'Ожидание подтверждения',
			'rejected' => 'Отклонён',
			'paid' => 'Оплачен'
		))
	);

}
