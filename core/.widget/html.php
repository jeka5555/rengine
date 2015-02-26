<?php

namespace Widgets;

// Отображение обычного HTML
// -------------------------
class HTML extends \Widget {

	// Информация компонента
	// ---------------------
	public static $component = array(
		'type' => 'widget',
		'id' => 'html',
		'title' => 'HTML',
		'group' => 'generic',
		'editable' => true,
		'description' => 'Виджет предназначен для вывода фрагмента HTML-кода',
	);

	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'html' => array('id' => 'html', 'type' => 'text', 'type' => 'textarea', 'isHTML' => true, 'title' => 'Текст')
		);
	}

	// Рендер
	// ------
	public function render() {
	    return @ $this->args['html'];
	}
}