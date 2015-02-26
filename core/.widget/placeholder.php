<?php

namespace Core\Widgets;

class Placeholder extends \Widget {

	// Component data
	// --------------
	public static $component = array(
		'id' => 'placeholder',
		'title' => 'Группировка вывода',
		'tag' => false
	);

	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'content' => array('type' => 'text', 'title' => 'Контент')
		);
	}

	// Render function
	// ---------------
	public function render() {
		return @ $this->args['content'];
	}

}
