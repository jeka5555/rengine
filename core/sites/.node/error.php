<?php

namespace Core\SiteNodes;

class Error extends \Node {

	// Component
	// ---------
	public static $component = array(
		'id' => 'error',
		'title' => 'Обработка ошибок',
	);

	// Node color
	// ----------
	public static $nodeColor = '#FFDDDD';


	// Get node data format
	// --------------------
	public static function getNodeDataFormat() {
		return array(
			'errorCode' => array('type' => 'number', 'title' => 'Код ошибки'),
			'errorText' => array('type' => 'text', 'title' => 'Текст ошибки'),
			'errorPageTemplate' => array('type' => 'text', 'title' => 'Шаблон страницы ошибки')
		);
	}

	// Execute node code
	// -----------------
	public function execute() {

		// Get error template
		// ------------------
		$errorTemplate = 'error';
		if (!empty($this->errorTemplate)) $errorTemplate = $this->errorTemplate;

		// Set page template
		// -----------------
		\Core::getApplication()->data['page']['template'] = $errorTemplate;

	}
}
