<?php

namespace Core\Rules;

class False extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'false',
		'title' => 'Никогда'
	);

	public static $ruleFormat = array();

	// Check rule
	// ----------
	public function check() {
		return false;
	}

}