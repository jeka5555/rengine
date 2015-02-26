<?php

namespace Core\Rules;

class Path extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'path',
		'title' => 'Проверка элемента пути'
	);

	// Rule format
	// -----------
	public static $ruleFormat = array(
		'index' => array('type' => 'number', 'title' => 'Индекс узла'),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция'),
		'value' => array('type' => 'text', 'title' => 'Значение')
	);

	// Check rule
	// ----------
	public function check() {

		// Get data
		// --------
		$path = \Core::getApplication()->request->path;
		$pathElement = @ $path[$this->index];

		// Eval operation
		// --------------
		$result = \Rules::evalOperation($pathElement, @ $this->value, @ $this->operation);
		return $result;
	}

}