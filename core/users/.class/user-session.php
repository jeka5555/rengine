<?php

namespace Users\Classes;

class UserSession extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'userSession',
		'title' => 'Пользовательская сессия'
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
		'userID' => array('type' => 'object', 'class' => 'user', 'useID' => true, 'title' => 'Пользователь'),
		'sessionID' => array('type' => 'text', 'title' => 'Идентификатор сессии'),
		'loginTime' => array('type' => 'text', 'title' => 'Время входа'),
		'ip' => array('type' => 'text', 'title' => 'Адрес с которого был вход'),
		'data' => array('type' => 'record', 'title' => 'Произвольные данные сессии')
	);
}