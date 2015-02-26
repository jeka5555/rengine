<?php

namespace Core\Users\Rules;

class UserID extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'userID',
		'title' => 'Определенный пользователь'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'user' => array('type' => 'object', 'title' => 'Пользователь', 'class' => 'user' )
	);

	// Check rule
	// ----------
	public function check() {
		return (\Core::getModule('users')->user->_id === $this->value);
	}

}