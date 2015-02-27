<?php

namespace Core\SiteNodes;

class StaticContent extends \Node {

	// Component
	// ---------
	public static $component = array(
		'id' => 'static-content',
		'title' => 'Статичный контент',
	);

	// Node color
	// ----------
	public static $siteNodeColor = '#FFFF00';

	// Get format of this node
	// -----------------------
	public static function getNodeDataFormat() {

		$format = array(
			'content' => array('type' => 'contentEditor', 'title' => 'Содержимое страницы'),
			'contentBlock' => array('type' => 'text', 'title' => 'Блок для вывода')
		);

		return $format;

	}


	// Execute node code
	// -----------------
	public function executeNode() {

        parent::executeNode();

		// Get content block
		// -----------------
		$settings = \Core::getModule('content')->getComponentSettings();
		$contentBlock = first_var(@ $this->data['contentBlock'], @ $settings['contentBlock'], 'content');

		// Add content widget
		// ------------------
		\Core::getModule('widgets')->addWidget(array('content',
			array('content' => @$this->data['content']),
			array('block' => $contentBlock)
		));
		
		parent::executeNode();

	} 

}
