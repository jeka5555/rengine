<?php

namespace Core\Rules;

class AndRule extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'and',
		'title' => 'И'
	);

	// Format
	// ------
	public static $ruleFormat = array(
		'rules' => array('type' => 'rules', 'title' => 'Правила')
	);

	// Check rule
	// ----------
	public function check() {
		return \Rules::check(@ $this->rules);
	}

}