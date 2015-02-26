<?php

namespace Core\Users\Rules;

class UserRole extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'userRole',
		'title' => 'Роль пользователя'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'role' => array('type' => 'text', 'title' => 'Роль')
	);

	// Check rule
	// ----------
	public function check() {
		return @ \Core::getModule('users')->hasRole($this->role);
	}

}