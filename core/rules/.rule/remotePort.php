<?php

namespace Core\Rules;

class RemotePort extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'remotePort',
		'title' => 'Порт клиента'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_SERVER['REMOTE_PORT'], @ $this->value, @ $this->operation);
		return $result;
	}

}