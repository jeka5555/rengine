<?php

namespace Core\DataViews;

class Cards extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'city'
	);

	// Отображение
	// -----------
	public function execute($args = array()) {

		// Берем значение
		// ---------------------
		$value = first_var(@ $this->value, '');

		$geoCityClass = \Core::getClass('geo-city');
		$city = $geoCityClass::findPK($value);


		return @$city->title;

	}
}
