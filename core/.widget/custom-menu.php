<?php

namespace Core\Widgets;

class CustomMenu extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'title' => 'Редактируемое меню',
		'editable' => true,
		'tag' => 'nav',
		'id' => 'custom-menu'
	);

	public function getWidgetArgsFormat() {
		return array(
			'items' => array('type' => 'list', 'title' => 'Элементы меню', 'format' => array('type' => 'object', 'class' => 'node'))
		);

	}

	// Render function
	// ---------------
	public function render() {
		if (!empty($this->args['items'])) return array('items' => $this->args['items']);
		else return false;
	}
}
