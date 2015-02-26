<?php

namespace Core\Widgets;

class PageTitle extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'type' => 'widget',
		'id' => 'page-title',
		'title' => 'Общие.Заголовок страницы',
		'group' => 'generic',
		'editable' => true,
		'description' => 'Вывод заголовка текущей страницы',
	);


	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'size' => array('type' => 'select', 'title' => 'Размер заголовка', 'values' => array(
				'1' => 'H1',
				'2' => 'H2',
				'3' => 'H3',
				'4' => 'H4'
			))
		);
	}

	// Render
	// ------
	public function render() {

		// Get page title
		// --------------
		$pageTitle = @ \Core::getApplication()->data['page']['title'];

		// Render
		// ------
		$size = first_var(@ $this->args['size'], 1);
        return '<h'.$size.'>'.$pageTitle.'</h'.$size.'>';
	}

}