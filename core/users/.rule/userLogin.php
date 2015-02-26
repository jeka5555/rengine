<?php

namespace Core\Users\Rules;

class UserLogin extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'userLogin',
		'title' => 'Логин пользователя'
	);

	// Check rule
	// ----------
	public function check() {
		return \Rules::evalOperation(@ \Core::getModule('users')->user->nick, @ $this->value, @ $this->operation);
	}

}