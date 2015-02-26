<?php

namespace Core\Users\Rules;

class UserGroup extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'userGroup',
		'title' => 'Пользователь входит в группу'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'group' => array('type' => 'object', 'title' => 'Группа', 'class' => 'userGroup')
	);

	// Check rule
	// ----------
	public function check() {
		return @ \Core::getModule('users')->isInsideGroup($this->group);
	}

}