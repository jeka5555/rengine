<?php

class Templates extends Module {

	// Component
	// ---------
    public static $component = array(
		'type' => 'module',
		'id' => 'template',
	    'title' => 'Шаблоны',
		'description' => 'Движок шаблонов'
	);

	// Render template
	// ---------------
	public static function get($template, $data = array()) {

		// Engine ID
		// ---------
		$engineID = null;

		// Get template data directly
		// --------------------------
		if (is_array($template)) {
			$templateData = $template;
			$engineID = $template['engineType'];
		}

		// Load from database
		// ------------------
		else if (is_string($template)) {

			// Load from database
			// ------------------
			$templateClass = \Core::getClass('template');
			$templateObject = $templateClass::findOne(array('query' => array(
				'id' => $template
			)));

			// Get data
			// --------
			if (!empty($templateObject)) {
				$engineID = $templateObject->engineType;
				$templateData = $templateObject->properties;
			}

			//Try to find embedded template
			// ----------------------------
			else {
				$templateData = \Core::getComponent('template', $template);
				if (!empty($templateData)) $engineID = 'classBased';
			}
		}

		// Template object is not found
		// ----------------------------
		if (empty($templateData)) {
			\Logs::log('Шаблон '.$template.' не найден', 'warning');
			return;
		}

		// Get engine type
		// ---------------
		$engine = \Core::getComponent('templateEngine', $engineID);
		if (empty($engine)) {
			\Logs::log('Движок шаблонизатора '.$engineID.' не найден', 'warning');
			return;
		}

		// Render template
		// ---------------
		return $engine::render($templateData, $data);
	}

}
