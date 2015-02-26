<?php

namespace Core\Rules;

class ProcessedSiteNode extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'processedSiteNode',
		'title' => 'Активированный узел'
	);

	public static $ruleFormat = array(
		'siteNodeID' => array('type' => 'object', 'title' => 'Узел сайта', 'class' => 'site-node'),
		'operation' => array('type' => 'ruleOperation', 'title' => 'Операция')
	);

	// Check rule
	// ----------
	public function check() {

		// Get nodes list
		// --------------
		$siteNodes = @ \Core::getModule('sites')->$processedNodes;
		if (empty($siteNodes)) return false;

		// Or test
		// -------
		$result = in_array(@ $this->siteNodeID, $siteNodes);
		return $result;
	}

}