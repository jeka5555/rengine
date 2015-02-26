<?php

namespace Core\Rules;

class OrRule extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'or',
		'title' => 'ИЛИ'
	);

	// Format
	// ------
	public static $ruleFormat = array(
		'rules' => array('type' => 'rules', 'title' => 'Правила')
	);

	// Check rule
	// ----------
	public function check() {
		return \Rules::check(@ $this->rules, 'or');
	}

}