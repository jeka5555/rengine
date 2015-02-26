<?php

// Отображение данных
// -------------------------------
class DataView extends \Module {

	public static $component = array(
		'type' => 'module',
		'id' => 'data-views',
		'title' => 'Форматированный вывод'
	);

	// Прямой вывод данных
	// ----------------------------
	public static function get($view, $value = null, $options = array()) {

		// Поиск расширения для отображения
		// --------------------------------
		if ($viewClass = \Core::getComponent('dataView', $view)) {

			// Create view instance
			// --------------------
			$view = $viewClass::getInstance();
			$view->value = $value;
			$view->options = $options;

			// Return result
			// -------------
			return $view->execute();

		}

		return '';
	}

}
