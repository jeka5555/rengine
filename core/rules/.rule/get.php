<?php

namespace Core\Rules;

class GET extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'get',
		'title' => 'Переменная GET'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_GET[$this->variable], @ $this->value, @ $this->operation);
		return $result;
	}

}