<?php

namespace Core\Users\Rules;

class UserLogged extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'userLogged',
		'title' => 'Пользователь вошел на сайт'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array();

	// Check rule
	// ----------
	public function check() {
		return !empty(\Core::getModule('users')->user);
	}

}