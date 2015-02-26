<?php

namespace Templates;

class Menu extends \Template {

	// Component data
	// --------------
	static $component = array(
		'type' => 'template',
		'id' => 'menu',
		'title' => 'Настраиваемое меню'
	);

	// Render one menu item
	// --------------------
	private function renderElement($element, $level = 0) {

		$content = '';

		// What level is in output
		// -----------------------
		if (empty($level)) $level = 0;

		// Set element classes
		// -------------------
		$htmlClasses = array('menu-item');
		if (@ $element['active'] == true) $htmlClasses[] = 'active';
		if (isset($element['htmlClass'])) $htmlClasses[] += $element['htmlClass'];

		// Add childern
		// ------------
		if (!empty($element['children'])) {

			$children = '';

			// Get each children data
			// ----------------------
			foreach ($elementData['children'] as $subitem) {
				$children .= $this->renderElement($subitem, $level + 1);
			}

			// Collect children content to wrapper tag
			// ---------------------------------------
			$content .= \Content::buildTag(array(
				'tag' => 'li',
				'htmlClasses' => array('level-'.$level, 'menu-item-children'),
				'content' => $children
			));


		}

		// Create output tag
		// -----------------
		$content = \Content::buildTag(array(
			'htmlID' => @$element['htmlID'],
			'htmlClasses' => $htmlClasses,
			'tag' => 'ul',
			'content' => $content
		));

		return $content;

	}


	// Render menu
	// -----------
	public function render() {

		// If emtpy, no menu will be drawn
		// -------------------------------
		if (empty($this->items)) return '';

		// Set class
		// ---------
		$classes = array('level-0');
		if (@ $this->menuClass) $classes[] = $this->menuClass;

		// Add children
		// ------------
		$content = '';
		foreach($this->children as $item) {
			$content .= $this->renderElement($item, 1);
		}

		// Возврат содержимого
		// ---------------------
		$content = \Content::buildTag(array(
			'tag' => 'menu',
			'htmlClasses' => $classes,
			'content' => $content
		));

		return $content;

	}

}
