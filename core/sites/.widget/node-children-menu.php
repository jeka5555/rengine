<?php

namespace Core\Widgets;

class NodeChildrenMenu extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'title' => 'Меню потомков узла',
		'editable' => true,
		'id' => 'node-children-menu'
	);

	// Widget args
	// -----------
	public function getWidgetArgsFormat() {
		return array(
			'menuID' => array('type' => 'text', 'title' => 'Идентификатор меню'),
			'types' => array('type' => 'list', 'title' => 'Отображаемые типы', 'format' => array('type' => 'text')),
			'openNotActiveNodes' => array('type' => 'boolean', 'title' => 'Все узлы раскрыты'),
			'maxDepth' => array('type' => 'number', 'title' => 'Максимальная глубина'),
			'ignoreMenuID' => array('type' => 'boolean', 'title' => 'Игнорировать ID меню для элементов'),
			'ignoreMenuVisibility' => array('type' => 'boolean', 'title' => 'Игнорировать видимость'),
		);
	}

	// Render item
	// -----------
	public function renderItem($item, $level = 0) {

		$itemNode = \Node::getNodeObject($item);

		// Options
		// -------
		$nodeIsOpened = false;
		$classAddin = '';

		$id = 'node-item-'.uniqid();

		// Title
		// -----
		$content = '<a class="'.$id.'" href="'.$itemNode->getURI().'">'.$itemNode->title.'</a>';

		// Attach controller
		// -----------------
		\Core::getModule('objects')->attachController(array(
			'widget' => '.'.$id,
			'class' => 'node',
			'id' => $item->_id
		));

		// If node is open
		// ---------------
		if (in_array($itemNode->_id, \Core::getModule('sites')->nodes)) {
			$nodeIsOpened = true;
			$classAddin .= ' opened';
		}

		// If node is active
		// -----------------
		if (\Core::getModule('sites')->currentNode == $itemNode->_id) {
			$classAddin .= ' active';
		}

		// Get children
		// ------------
		if (@ $this->args['maxDepth'] >= $level) {

			// Node
			// ----
			if ($nodeIsOpened == true || @ $this->args['openNotActiveNodes'] == true) {

				// Get children
				// ------------
				$children = @ $this->getChildren($itemNode->_id);
				if (!empty($children)) {

					$childrenContent = '';

					// Render each child
					// -----------------
					foreach($children as $child) {
						$childrenContent .= $this->renderItem($child, $level + 1);
					}

					// Join children content
					// ---------------------
					$content .= '<ul class="level-'.$level.'">'.$childrenContent.'</ul>';
				}

			}
		}


		// Return content
		// --------------
		return '<li class="level-'.$level.' '.$classAddin.'">'.$content.'</li>';
	}


	// Get children nodes for menu
	// ---------------------------
	public function getChildren($nodeID = null) {

		// Get menu class
		// --------------
		$nodeClass = \Core::getClass('node');


		// Build query
		// -----------
		$query = array(
			'parent' => $nodeID,
			'hidden' => array('$ne' => true),
			'isSystem' => array('$ne' => true)
		);

		// Types filter
		// ------------
		if (!empty($this->args['types'])) {
			$query['type'] = array('$in' => $this->args['types']);
		}

		// If one type has been passed
		// ---------------------------
		else if (!empty($this->args['type'])) {
			$query['type'] = $this->args['type'];
		}

		// Get children
		// ------------
		$children = $nodeClass::find(array('query' => $query, 'sort' => array('options.menu.order' => 1, 'title' => 1)));
		return $children;

	}

	// Render function
	// ---------------
	public function render() {

		// Cancel if current node is not exists
		// ------------------------------------
		if (empty(\Core::getModule('sites')->currentNode)) {
			$this->cancel();
			return;
		}

		// Get children
		// ------------
		$children = $this->getChildren(\Core::getModule('sites')->currentNode);

		if (empty($children)) {
			$this->cancel();
		}

		$content = '';
		foreach($children as $child) {
			$content .= $this->renderItem($child, 0);
		}

		// Return content
		// --------------
		return '<h3 class="menu-title">Навигация</h3><ul>'.$content.'</ul>';
	}
}
