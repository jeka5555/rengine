<?php

namespace Core\Widgets;

class NodesMenu extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'title' => 'Меню узла',
		'editable' => true,
		'id' => 'nodes-menu'
	);

	// Widget args
	// -----------
	public function getWidgetArgsFormat() {
		return array(
			'menuID' => array('type' => 'text', 'title' => 'Идентификатор меню'),
			'root' => array('type' => 'object', 'title' => 'Основной узел', 'class' => 'node'),
			'types' => array('type' => 'list', 'title' => 'Отображаемые типы', 'format' => array('type' => 'text')),
			'openNotActiveNodes' => array('type' => 'boolean', 'title' => 'Все узлы раскрыты'),
			'maxDepth' => array('type' => 'number', 'title' => 'Максимальная глубина'),
			'icon' => array('type' => 'dependent', 'title' => 'Выводить иконки', 'format' => array(
				'width' => array('type' => 'number', 'title' => 'Ширина', 'value' => 100),
				'height' => array('type' => 'number', 'title' => 'Высота', 'value' => 100),
				'mode' => array('type' => 'text', 'title' => 'Режим обрезки', 'value' => 'cover'),
			)),
			'ignoreMenuID' => array('type' => 'boolean', 'title' => 'Игнорировать ID меню для элементов'),
			'ignoreMenuVisibility' => array('type' => 'boolean', 'title' => 'Игнорировать видимость'),
			'addRootNode' => array('type' => 'boolean', 'title' => 'Добавлять родительский'),
			'rootNodeTitle' => array('type' => 'text', 'title' => 'Имя родительского узла')
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

		$id = $itemNode->_id;

		// Icon
		// -----
		if(@$this->args['icon'] == true and !empty($itemNode->options['menu']['icon'])) $icon = '<div class="icon">'.\DataView::get('media', $itemNode->options['menu']['icon'], array('width' => first_var(@$this->args['icon']['width'], 100), 'height' => first_var(@$this->args['icon']['height'], 100), 'mode' => first_var(@$this->args['icon']['mode'], 'cover'))).'</div>';
		else $icon = '<div class="icon"></div>';                                  
    
    if (@$itemNode->options['menu']['notLink'] != true) {
        $href = ' href="'.$itemNode->getURI().'"';
    }

		// Title
		// -----
		$title = first_var(@$itemNode->options['menu']['title'], $itemNode->title);
		$content = '<a'.@$href.'>'.@$icon.'<span class="title">'.$title.'</span></a>';


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
		if (@ $this->args['maxDepth'] > $level) {

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
		return '<li data-node-id="'.$id.'" class="level-'.$level.' '.$classAddin.'">'.$content.'</li>';
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

		// Ignore visibility
		// -----------------
		if (@ $this->args['ignoreMenuVisibility'] != true) {
			$query['options.menu.enable'] = true;
		}

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

		// Cities filter
		// -------------
		$query['$and'] = array();

		$cityFilter = array(
			'$or' => array(
				array('cities' => \Core::getModule('geo')->city),
				array('cities' => null),
				array('cities' => array('$size' => 0))
			)
		);
		$query['$and'][] = $cityFilter;


		// Filter by menu ID
		// -----------------
		if (!empty($this->args['menuID'])) {

			// If not ignore menu ID
			// ----------------------
			if (@ $this->args['ignoreMenuID'] != true) {
				$query['$or'][] = array('options.menu.menuList' => $this->args['menuID']);
				$query['$or'][] = array('options.menu.menuList' => null);
				$query['$or'][] = array('options.menu.menuList' => array('$size' => 0));
			}
		}

		// Get children
		// ------------
		$children = $nodeClass::find(array('query' => $query, 'sort' => array('options.menu.order' => 1, 'title' => 1)));

		return $children;

	}

	// Render function
	// ---------------
	public function render() {



		// Get children
		// ------------
		$children = $this->getChildren(@ $this->args['root']);
		if (empty($children)) return;

		$content = '';
		foreach($children as $child) {
			$content .= $this->renderItem($child, 0);
		}


		// Add parent
		// ----------
		$parent = '';
		if (!empty($this->args['root']) && @ $this->args['addRootNode'] == true) {

			$nodeClass = \Core::getClass('node');
			$root = $nodeClass::findPK($this->args['root']);
			$root = \Node::getNodeObject($root);

			$title = first_var(@$root->options['menu']['title'], @ $this->args['rootNodeTitle'], $root->title);

			// Icon
			// -----
			if(@$this->args['icon'] == true and !empty($root->options['menu']['icon'])) $icon = '<div class="icon">'.\DataView::get('media', $root->options['menu']['icon'], array('width' => first_var(@$this->args['icon']['width'], 100), 'height' => first_var(@$this->args['icon']['height'], 100), 'mode' => first_var(@$this->args['icon']['mode'], 'cover'))).'</div>';
			else $icon = '<div class="icon"></div>';

			$parent = '<li class="level-0"><a href="'.$root->getURI().'">'.@$icon.'<span class="title">'.$title.'</span></a></li>';
		}
		
		
		// Добавим id виджету
		if (!empty($this->args['menuID'])) {
			$this->options['htmlID'] = $this->args['menuID'];
		}

		// Return content
		// --------------
		return '<ul>'.$parent.$content.'</ul>';
	}
}
