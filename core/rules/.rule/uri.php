<?php

namespace Core\Rules;

class URI extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'uri',
		'title' => 'Анализ ссылки'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'uri' => array('type' => 'text', 'title' => 'Ссылка'),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция')
	);

	// Check rule
	// ----------
	public function check() {
		$result = \Rules::evalOperation(@ $_SERVER['REQUEST_URI'], @ $this->uri, @ $this->operation);
		return $result;
	}

}