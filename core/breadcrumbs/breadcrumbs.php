<?php

namespace Core\Modules;

class Breadcrumbs extends \Module {

	public static $component = array(
		'id' => 'breadcrumbs',
		'title' => 'Хлебные крошки',
		'description' => 'Модуль отвечает за организацию хлебных крошек на сайте. Обеспечивает интерфейс для создания блоков хлебных крошек, манипуляции и выводом их на сайт'
	);

	public $breadcrumbs = array(); // Breadcrumbs storage

	// Clear breadcrumbs
	// -----------------
	public function clearBreadcrumbs($breadcrumbsID = null) {
		if ($breadcrumbsID == null) $this->breadcrumbs = array();
		else ($this->breadcrumbs[$breadcrumbsID] = array());
	}


	// Get path to node
	// ----------------
	public function getNodeBreadcrumbs($node) {

		$nodeClass = \Core::getClass('node');

		// Collect here
		// ------------
		$result = array();


		if (!empty($node->parent)) {
			do {
				$parent = $nodeClass::findPK($node->parent);
				if ($parent->type == 'site') break;
				$nodeObject = \Node::getNodeObject($parent);
				$result[] = array('title' => $parent->title, 'link' => $nodeObject->getURI());
				$node = $parent;
			} while(!empty($node->parent));
		}

		$result[] = array('title' => 'Главная', 'link' => '/');

		$result = array_reverse($result);

		return $result;
	}

	// Append breadcrumbs
	// ------------------
	public function appendBreadcrumbs($item, $breadcrumbsID = 'default') {

		// Guards
		// ------
		if (empty($item) || !is_array($item)) return;

		// Append item
		// ------------
		$this->breadcrumbs[$breadcrumbsID][] = $item;
	}

	// Get breadcrumbs
	// ----------------
	public function getBreadcrumbs($breadcrumbsID = 'default') {
		return @ $this->breadcrumbs[$breadcrumbsID];
	}

	// Set breadcrumbs
	// ----------------
	public function setBreadcrumbs($breadcrumbsID = 'default', $items) {
		$this->breadcrumbs[$breadcrumbsID] = $items;
	}
}
