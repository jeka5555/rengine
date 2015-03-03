<?php

namespace Core\Components;

class Rule extends \Component {

	// Init component
	// --------------
	public static $component = array(
		'id' => 'rule',
		'type' => 'component',
		'autoload' => true,
		'title' => 'Правило'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'value' => array('type' => 'text', 'title' => 'Значение'),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция')
	);

	public $invert = false;

}