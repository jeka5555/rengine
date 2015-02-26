<?php

namespace Core\DataViews;

class Text extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'text'
	);

	// Отображение
	// -----------
	public function execute($args = array()) {

        if (!is_string($this->value)) return '';

		// Берем значение
		// ---------------------
		$value = first_var(@ $this->value, '');

		// Escape
		// ------
		if (@ $this->options['escape'] == true && !empty($value)) {
			$value = htmlspecialchars($value);
		}


		// Strip tags
		// ----------
		if (@ $this->options['stripTags'] == true && !empty($value)) {
			$value = strip_tags($value);
		}

		// Обрезка
		// ---------------------
		if (isset($this->options['maxChars'])) {
			$value = mb_strimwidth($value, 0, @ $this->options['maxChars'], '...');
			}
		return $value;

	}
}
