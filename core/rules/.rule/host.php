<?php

namespace Core\Rules;

class Host extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'host',
		'title' => 'Имя хоста'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'host' => array('type' => 'text', 'title' => 'Имя хоста'),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция')
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_SERVER['HTTP_HOST'], @ $this->host, @ $this->operation);
		return $result;
	}

}