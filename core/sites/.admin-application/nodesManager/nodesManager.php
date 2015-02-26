<?php

namespace Core\Admin\AdminApplications;

class NodesManager extends \Core\Admin\Components\AdminApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'nodesManager',
		'addOnToolbar' => true,
		'title' => 'Структура',
		'icon' => '/core/sites/.admin-application/nodesManager/icon.png',
		'access' => array(
			array(
				'type' => 'or',
				'rules' => array(
					array('type' => 'userRole', 'role' => 'super'),
					array('type' => 'userRole', 'role' => 'administrator'),
					array('type' => 'userRole', 'role' => 'seo'),
					array('type' => 'userRole', 'role' => 'content-manager')
				)
			)
		)
	);

	// Get item info
	// -------------
	private static function getPathItem($item) {

		// Get node
		// --------
		$nodesClass = \Core::getClass('node');
		$node = $nodesClass::findPK($item['id']);

		// Если ничего нет, то облом
		// -------------------------
		if (empty($node)) return false;
		if (empty($node->parent)) return false;

		// Parent
		// ------
		$parent = $nodesClass::findPK($resource->parent);
		return array('id' => $parent->_id, 'title' => first_var($parent->title, "Новая папка"));
	}


	// Get path
	// --------
	public function commandGetPath($args = array()) {

		$nodesClass = \Core::getClass('node');
		if (empty($nodesClass)) return;

		// Build
		// -----
		$path = array();

		// Set parent node
		// ---------------
		if (!empty($args['parent'])) $nodeID = $args['parent'];

		if (!empty($args['target'])) {
			$targetNode = $nodesClass::findPK($args['target']);
			$nodeID = $targetNode->parent;
		}

		// Replace
		// -------
		if (!empty($nodeID)) {
			while ($child = $nodesClass::findPK($nodeID)) {
				$path[] = array('title' => first_var(@$child->title, @ $child->alias, @ $child->_id ), 'id' => $child->_id);
				$nodeID = $child->parent;
			}
		}

		$path[] = array('title' => '.');

		// Return
		// ------
		return array_reverse($path);

	}




	// Get current node tree
	// ---------------------
	public function getTree($nodeID = null) {

		// Get node class
		// --------------
		$nodeClass = \Core::getComponent('class', 'node');

		// Load children nodes
		// -------------------
		$childrenNodes = array();
		$children = $nodeClass::find(array('query' => array('parent' => $nodeID), 'sort' => array('title' => 1)));

		// Load
		// ----
		if (!empty($children))
		foreach($children as $child) {

			// Get count
			// ---------
			$count = $nodeClass::find(array('query' => array('parent' => $child->_id), 'count' => true));

			// Add to tree
			// -----------
			if ($count > 0) {
				$childrenNodes[] = array('id' => $child->_id, 'title' => first_var(@$child->title, @$child->alias, @$child->_id));
			}
		}


		// Get node
		// --------
		if (empty($nodeID)) return $childrenNodes;

		// Add current node
		// ----------------
		$node = $nodeClass::findPK($nodeID);
		$childrenNodes = array(array('id' => $node->_id, 'title' => $node->title, 'children' => $childrenNodes, 'active' => true));

		// Add all parents
		// ---------------
		while (!empty($node->parent)) {
				$node = $nodeClass::findPK($node->parent);
				$childrenNodes = array(array('id' => $node->_id, 'title' => $node->title, 'children' => $childrenNodes));
		}


		// Append root and return
		// ----------------------
		$childrenNodes = array(array('title' => '/', 'children' => $childrenNodes));
		return $childrenNodes;
	}


	// Read table
	// ----------
	private function getResourcesTable($args = array()) {

		$nodes = $this->commandGetResourcesTable();
		return $nodes;
	}


	// Update
	// ------
	public function commandUpdate($args = array()) {

		// Get classes
		// -----------
		$nodesClass = \Core::getClass('node');
		$userClass = \Core::getClass('user');

		// Build query here
		// ----------------
		$query = array();

		// Query
		// -----
		if (@ $args['mode'] == 'tree') {

			// Generic
			// -------
			$query = array('parent' => null );
			if (!empty($args['parent'])) $query['parent'] = $args['parent'];

			// Targeting
			// ---------
			if (!empty($args['target'])) {
				$targetNode = $nodesClass::findPK($args['target']);
				$query['parent'] = $targetNode->parent;
			}
		}

		// Add filters
		// -----------
		if (!empty($args['filters'])) {
			if (!empty($args['filters']['@text'])) {
				$query['title'] = new \MongoRegex('/.*'.$args['filters']['@text'].'.*/iu');
			}
		}
		// Hide system
		// -----------
		$query['isSystem'] = array('$ne' => true);

		// Get type
		// --------
		if (!empty($args['type'])) {
			$type = \Core::getComponent('node', $args['type']);
			$query['type'] = $args['type'];
		}

		// Return format
		// -------------
		$tableFormat = array(
			array('id' => 'title', 'title' => 'Название', 'sortable' => true),
			array('id' => 'path', 'title' => 'Путь', 'css' => array('color' => '#666','font-size' => '11px'), 'sortable' => true),
			array('id' => 'type', 'title' => 'Тип', 'sortable' => true, 'css' => array('color' => '#666','font-size' => '11px'),),
			array('id' => 'hidden', 'title' => 'Видимость', 'sortable' => true),
			array('id' => 'childrenCount', 'title' => 'Узлов'),
			array('id' => '@createTime', 'title' => 'Дата', 'sortable' => true)
		);

		// Count
		// -----
		$count = $nodesClass::find(array('query' => $query, 'count' => true));
		$page = first_var(@ $args['page'], 0);
		$pagesCount = ceil($count / 10);

		// Pagination
		// ----------
		$skip = $page * 10;

		// Sort
		// ----
		$sort = array('title' => 1);
		if (!empty($args['sort'])) $sort = $args['sort'];

		// Read resources
		// --------------
		$nodes = $nodesClass::find(array('query' => $query, 'limit' => 10, 'skip' => $skip, 'sort' => $sort));

		// Process to array
		// ----------------
		if (!empty($nodes))
		foreach($nodes as $node) {

			// Type info
			// ---------
			$typeName = 'общий';

			if (!empty($node->type)) {

				$typeComponent = @ \Core::getComponent('node', $node->type);

				// If class is exists, get it's name
				// ---------------------------------
				if (!empty($typeComponent)) {
					$typeName = first_var( @$typeComponent::$component['title'], @ $typeComponent::$component['id']);
				}
			}

			// Get children count
			// ------------------
			$childrenCount = $nodesClass::find(array('query' => array('parent' => $node->_id), 'count' => true));

			// Icons
			// -----
			$icons = array();
			if ($node->hidden == true) $icons[] = 'hidden';


			// Append output table
			// -------------------
			$tableData[] = array(
				'_id' => $node->_id,
				'icons' => $icons,
				'title' => $node->title,
				'typeID' => $node->type,
				'type' => $typeName,
				'path' => @ $node->properties['path']   ,
				'hidden' => (@ $node->hidden == true) ? 'нет': 'да',
				'childrenCount' => $childrenCount,
				'@createTime' => \DataView::get('datetime', $node->get('@createTime'), array('format' => '%d.%m.%Y'))
			);
		}


		// Return data
		// -----------
		return array(
			'tree' => $this->getTree(@ $args['parent']),
			'path' => @ $this->commandGetPath($args),
			'tableFormat' => $tableFormat,
			'tableSort' => $sort,
			'tableData' => @ $tableData,
			'pagesCount' => $pagesCount,
			'page' => $page,
			'types' => @ $this->getNodesTypes()
		);

	}


	// Get list of resource types
	// --------------------------
	public function getNodesTypes() {

		\Extension::$ext['node'];

		$types = array();

		// Collect from extensions
		// -----------------------
		if (!empty(\Extension::$ext['node'])) {
			foreach(\Extension::$ext['node'] as $typeID => $type) {

			  $typeComponent = \Core::getComponent('node', $typeID);

				$types[$typeID] = array(
					'_id' => $typeID,
					'alias' => $typeID,
					'title' => first_var( @ $typeComponent::$component['title'], $typeID)
				);
			}
		}

		// Return list
		// -----------
		return $types;
	}



	// Init manager
	// ------------
	public function commandInit($args = array()) {


		// Detect viewer component
		// -----------------------
		$viewerComponent = 'default';

		$nodeClass = \Core::getClass('node');

		if (!empty($args['nodeID'])) {

			// Get node
			// --------
			$node = $nodeClass::findPK($args['nodeID']);

			if (empty($node)) return;

			// Get viewer component
			// --------------------
			$nodeObject = \Node::getNodeObject($node);
			$class = get_class($nodeObject);
			if (property_exists($class, 'nodeViewerComponent')) {
				$viewerComponent = $class::$nodeViewerComponent;
			}
		}


		// Append system nodes
		// -------------------
		$systemNodes = array();

		$systemNodesList = $nodeClass::find(array('query' => array(
			'parent' => @ $args['nodeID'],
			'isSystem' => true
		)));

		if (!empty($systemNodesList)) {
			foreach ($systemNodesList as $systemNode) {
				$systemNodes[] = array(
					'id' => $systemNode->_id,
					'title' => $systemNode->title
				);
			}
		}

		// Return result
		// -------------
		return array(
			'systemNodes' => $systemNodes,
			'viewerComponent' => $viewerComponent,
			'tree' => $this->getTree(@ $args['nodeID'])
		);

	}

	// Collect node's widgets
	// -----------------------
	public function commandGetNodeWidgets($args = array()) {

		$widgets = array();

		// Read first node
		// ---------------
		$nodeClass = \Core::getClass('node');
		$widgetClass = \Core::getClass('widget');

		$node = $nodeClass::findPK($args['nodeID']);

		// Add widgets to list
		// -------------------
		$element = array('id' => @ $node->_id, 'path' => @ $node->path, 'depth' => 1);

		if (!empty($node->widgets)) {

			// Append widgets
			// --------------
			foreach($node->widgets as $widget) {

				// Widget
				// ------
				$widgetObject = $widgetClass::findPK($widget['id']);
				if (empty($widgetObject)) continue;

				$typeID = @ $widgetObject->type;

				// Get component
				// -------------
				$typeTitle = 'отсутствует';
				$widgetComponent = \Core::getComponent('widget', $widgetObject->type);
				if (!empty($widgetComponent)) {
					$typeTitle = first_var(@ $widgetComponent::$component['title'], @ $widgetComponent::$component['id']);
				}
				else {
					$typeID .= ' (отсутствует)';
				}


				// Add widget
				// ----------
				$widgets[] = array(
					'id' => @ $widget['id'],
					'type' => $typeID,
					'typeTitle' => $typeTitle,
					'title' => @ $widgetObject->title,
					'role' => @ $widget['role'],
					'block' => @ $widgetObject->options['block'],
					'order' => @ $widgetObject->options['order'],
					'description' => @ $widgetObject->description
				);
			}

		}


		// Return widgets list
		// -------------------
		return $widgets;

	}

}
