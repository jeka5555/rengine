<?php

namespace Core\Rules;

class RemoteIP extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'remoteIP',
		'title' => 'IP адрес клиента'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_SERVER['REMOTE_ADDR'], @ $this->value, @ $this->operation);
		return $result;
	}

}