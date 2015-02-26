<?php

namespace Core\DataViews;


// Отображение даты и времени
// --------------------------
class DateTime extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'datetime'
	);

	// Return ago date
	// ---------------
	public function renderAgo() {

		// Calculate diff between dates
		// ----------------------------
		$referenceValue = first_var(@ $this->options['compareWith'], time());
		$reference = date_create();
		$reference->setTimestamp($referenceValue);

		$value = date_create();
		$value->setTimestamp($this->value);

		$diff = $value->diff($reference);

		// Accumulate here
		// ---------------
		$result = '';
		$positions = 0;

		// Add different elements
		// ----------------------
		if ($diff->y > 0 && $positions < 2) { $positions++;	$result .= $diff->y.' лет '; }
		if ($diff->m > 0 && $positions < 2) { $positions++;	$result .= $diff->m.' месяцев '; };
		if ($diff->d > 0 && $positions < 2) { $positions++;	$result .= $diff->d.' дней '; }
		if ($diff->h > 0 && $positions < 2) { $positions++;	$result .= $diff->h.' часов '; }
		if ($diff->i > 0 && $positions < 2) { $positions++;	$result .= $diff->i.' минут '; }
		if ($diff->s > 0 && $positions < 2) { $result .= $diff->s.' секунд ';	}

		return $result;

	}

	// Отображение
	// -----------
	public function execute() {

        if ($this->value == null) return '';

		if (@ $this->options['ago'] == true) return $this->renderAgo();
		return @strftime(first_var(@ $this->options['format'], '%d %h %Y'), $this->value);
	}
}
