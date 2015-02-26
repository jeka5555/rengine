<?php

namespace Core\DataViews;

// Отображение текста
// ------------------
class Number extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'number'
	);

	// Отображение
	// -----------
	public function execute($args = array()) {

		$ranks = array('', 'тыс.', 'млн.', 'млрд.', 'трл.');

		if (!empty($this->options['precision'])) {
			$this->value = round($this->value, $this->options['precision']);
		}

		// Берем значение
		// ---------------------
		$value = first_var(@ $this->value, 0);
		
		// Округление до единиц
		// --------------------
		$rank = 0;
		if (@$this->options['round'] == true) {
			while ($value/1000 >= 1) { $value = $value/1000; $rank++; }		
		}
		
		// Разделение 
		// --------------------
		$divider = '';
		if(isset($this->options['divider'])) {
			$value = @number_format($value, 0, ',', $this->options['divider']);		
		}

		return $value.' '.$ranks[$rank];

	}
}
