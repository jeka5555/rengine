<?php

namespace Core\Rules;

class Rand extends \Core\Components\Rule {

	public static $component = array(
		'id' => 'rand',
		'title' => 'Вероятность'
	);

	// Check rule
	// ----------
	public function check() {
		return (round(rand(0, $this->rate)) == 0);
	}

}