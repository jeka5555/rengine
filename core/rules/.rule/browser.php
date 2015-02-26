<?php

namespace Core\Rules;

class Browser extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'browser',
		'title' => 'Бразуер пользователя'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_SERVER['BROWSER'], @ $this->value, @ $this->operation);
		return $result;
	}

}