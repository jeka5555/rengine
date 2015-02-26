<?php

namespace Core\Rules;

class Request extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'request',
		'title' => 'Переменная REQUEST'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_REQUEST[$this->variable], @ $this->value, @ $this->operation);
		return $result;
	}

}