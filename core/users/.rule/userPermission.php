<?php

namespace Core\Users\Rules;

class UserPermission extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'userPermission',
		'title' => 'Права пользователя'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'permission' => array('type' => 'text', 'title' => 'Право')
	);

	// Check rule
	// ----------
	public function check() {
		return @ \Core::getModule('users')->hasPermission($this->permission);
	}

}