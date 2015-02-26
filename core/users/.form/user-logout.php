<?php

namespace Forms;

class UserLogout extends \Form {

	// Регистрация компонента
	// ----------------------
	public static $component = array(
		'type' => 'form',
		'id' => 'user-logout',
		'title' => 'Выход из системы'
	);

	// Form buttons
	// ------------
	public $buttons = array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Выход'));

	// Action URI
	// ----------
	public $submitURI = '/module/users/logout';
}
