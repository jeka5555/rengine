<?php

namespace Core\Rules;

class SiteID extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'siteID',
		'title' => 'Текущий сайт'
	);

	public static $ruleFormat = array(
		'siteID' => array('type' => 'object', 'title' => 'Сайт', ''),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция')
	);

	// Check rule
	// ----------
	public function check() {
		$siteID = @ \Core::getApplication()->data['siteID'];
		$result = \Rules::evalOperation(@ $siteID, @ $this->siteID, @ $this->operation);
		return $result;
	}

}