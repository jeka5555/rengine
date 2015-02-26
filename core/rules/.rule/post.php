<?php

namespace Core\Rules;

class POST extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'post',
		'title' => 'Переменная POST'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_POST[$this->variable], @ $this->value, @ $this->operation);
		return $result;
	}

}