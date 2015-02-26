<?php

namespace Core\Rules;

class True extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'true',
		'title' => 'Всегда'
	);

	public static $ruleFormat = array();

	// Check rule
	// ----------
	public function check() {
		return true;
	}

}