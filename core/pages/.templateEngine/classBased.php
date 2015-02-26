<?php

namespace TemplateEngines;

class ClassBased extends \TemplateEngine {

	// Информация о компоненте
	// -----------------------
	public static $component = array(
		'type' => 'templateEngine',
		'id' => 'classBased'
	);

	// Render template
	// ---------------
	public static function render($templateClass, $data = array()) {
		$templateObject = $templateClass::getInstance();
		$templateObject->properties = $data;
		return $templateObject->render();

	}
}
