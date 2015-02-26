<?php

namespace Core\Rules;

class SiteNode extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'siteNode',
		'title' => 'Текущий узел сайта'
	);

	public static $ruleFormat = array(
		'siteNodeID' => array('type' => 'object', 'title' => 'Узел сайта', 'class' => 'site-node'),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция')
	);

	// Check rule
	// ----------
	public function check() {

		// Nodes
		// -----
		$siteNodes = @ \Core::getModule('sites')->activatedNodes;

		// If nothing is here, return false
		// -------------------------------
		if (empty($siteNodes)) $result = false;

		// Or test
		// -------
		$result = in_array(@ $this->siteNodeID, $siteNodes);
		return $result;
	}

}