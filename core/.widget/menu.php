<?php

namespace Core\Widgets;

class Menu extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'title' => 'Именованное меню',
		'editable' => true,
		'id' => 'menu'
	);

	public function getWidgetArgsFormat() {
		return array(
			'menuID' => array('type' => 'text', 'title' => 'Идентификатор системного меню')
		);
	}


	// Render item
	// -----------
	public function renderItem($item, $level = 0) {

		// Get link
		// --------
		$link = first_var($item['link'], '#');

		$content = '<a href="'.$link.'">'.$item['title'].'</a>';

		// Get children
		// ------------
		$children = @ $item['items'];
		if (!empty($children)) {
			$childrenContent = '';

			// Render each child
			// -----------------
			foreach($children as $child) {
				$childrenContent .= $this->renderItem($child, $level + 1);
			}

			// Join children content
			// ---------------------
			$content .= '<ul>'.$childrenContent.'</ul>';
		}

		// Return content
		// --------------
		return '<li class="level-'.$level.'">'.$content.'</li>';
	}

	// Render function
	// ---------------
	public function render() {

		// Get menu module
		// ---------------
		$menuModule = \Core::getModule('menu');
		if (empty($menuModule)) return;

		// Get items
		// ---------
		$items = @ \Core::getModule('menu')->menu[$this->args['menuID']];
		if (empty($items)) return;

		// Items
		// -----
		$content = '';
		foreach($items as $item) {
			$content .= $this->renderItem($item, 0);
		}

		$this->options['htmlAttributes']['menu-id'] = $this->args['menuID'];

		return '<ul>'.$content.'</ul>';
	}
}
