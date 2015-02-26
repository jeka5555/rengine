<?php

namespace Core\Widgets;

class Content extends \Widget {

	// Component data
	// --------------
	public static $component = array(
		'id' => 'content',
		'title' => 'Отображение контента'
	);

	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'content' => array('type' => 'contentEditor', 'title' => 'Контент')
		);
	}

	// Render function
	// ---------------
	public function render() {
		return \DataView::get('content', @ $this->args['content']);
	}

}