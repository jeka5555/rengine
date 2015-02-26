<?php

namespace Core\Rules;

class RemoteHost extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'remoteHost',
		'title' => 'Имя хоста клиента'
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_SERVER['REMOTE_HOST'], @ $this->value, @ $this->operation);
		return $result;
	}

}