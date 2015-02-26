<?php


class Node extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'node',
		'autoload' => true,
		'title' => 'Узел структуры'
	);

	// This
	// ----
	public $params = array();


	// Get node Children Request Path
	// ------------
  public function getChildrenRequestPath($children, $childrenPath) {
		// Children path
		// -------------
		$childrenPath[] = first_var(@ $children->path, @ $children->_id);

		return $this->getRequestPath($childrenPath);
  }

	// Get node Request Path
	// ------------
	public function getRequestPath($childrenPath = array()) {
	
		// Node class
		// ---------
		$nodeClass = \Core::getClass('node');	
	
		// Ask parent for URI
		// ------------------
		$parentNode = $nodeClass::findPK($this->parent);
		if (!empty($parentNode)) {
			$node = \Node::getNodeObject($parentNode);
			return $node->getChildrenRequestPath($this, $childrenPath);
		} else {
			return array_reverse($childrenPath);
		}
			
	}

	// Get children URI
	// ----------------
	public function getChildrenURI($children) {

		// Children path
		// -------------
		$childrenPath = first_var(@ $children->path, @ $children->_id);

		// Return this node URI + children path
		// ------------------------------------
		$basePath = $this->getURI();
		return $basePath.'/'.$childrenPath;

	}  

	// Get node URI
	// ------------
	public function getURI() {

		// Get node path
		// -------------
		$nodePath = first_var(@ $this->path, @ $this->_id);

		// If node has no any parent, return as root node
		// ----------------------------------------------
		if (empty($this->parent)) {
			return '/'.$nodePath;
		}

		// Node class
		// ---------
		$nodeClass = \Core::getClass('node');

		// Ask parent for URI
		// ------------------
		$parentNode = $nodeClass::findPK($this->parent);
		if (!empty($parentNode)) {
			$node = \Node::getNodeObject($parentNode);
			return $node->getChildrenURI($this);
		}

		// Safe
		// ----
		return '#';

	}

	// Node object
	// -----------
	public static function getNodeObject($node) {

        if (!isset($node)) return;

		// Get node type ID
		// ----------------
		$nodeType = first_var(@ $node->type, 'generic');

		// Get node type
		// ------------
		$nodeComponent = \Core::getComponent('node', $nodeType);
		if (empty($nodeComponent)) {
			$nodeComponent = \Core::getComponent('node', 'generic');
		}

		// Create node
		// -----------
		$node = $nodeComponent::getInstance($node->properties);
		return $node;
	}

	// Get node color
	// --------------
	public static $siteNodeColor;
	public function getNodeColor() { return @ static::$siteNodeColor; }

	// Get node data format
	// --------------------
	public static function getNodeDataFormat() {}
	public static function getNodeDataStructure() {}

	// Build short node definition
	// ----------------------------
	public function getNodeDescription() {}

	// Test node activation
	// --------------------
	public function isEnabled() {

		// Skip not enabled nodes
		// ----------------------
		if (@ $this->enabled === false) return false;

		// Check rules
		// -----------
		if (!empty($this->enableRules)) {
			return \Rules::check($this->enableRules);
		}

		// True
		// ----
		return true;

	}
	
	// Get node parents path
	// -----------------
	public function getNodeParents($node = null, $parents = array()) {
	
		$node = first_var(@$node, $this);
		
		if (!empty($node->parent)) {
		
			// Node class
			// ---------
			$nodeClass = \Core::getClass('node');	
		
			$parentNode = $nodeClass::findPK($node->parent);
			$parents[] = $parentNode;
			return $this->getNodeParents($parentNode, $parents);
		} else {
			return $parents;
		}

	}
	
	
	// Get children nodes
	// ---------------------------
	public function getNodeChildren($node = null, $query = array()) {
           
		$node = first_var(@$node, $this);  

		$nodeClass = \Core::getClass('node');

		// Get children
		// ------------
		$children = $nodeClass::find(array(
			'query' => array_merge($query, array(
				'parent' => $node->_id,
				'hidden' => array('$ne' => true),
				'isSystem' => array('$ne' => true)
			)),
			'sort' => array('order' => 1, 'options.menu.order' => 1, 'title' => 1)
		));

		return $children;

	} 
	
	
	public function processNodeChildrens($node = null, $query = array()) {
	
		$node = first_var(@$node, $this);

		$nodeClass = \Core::getClass('node');	
		
		$children = $this->getNodeChildren($node, $query);

		if (!empty($children)) {
			foreach($children as $item) {
				$this->childrensNodes[] = $item;
				$this->processNodeChildrens($item, $query); 
			}
		}
		
	}
	
	// Get all childrens nodes
	// ---------------------------	
	public function getNodeChildrens($node = null, $query = array()) {
		$this->childrensNodes = array();
		$this->processNodeChildrens($node, $query);
		return $this->childrensNodes;
	}


	// Load node widgets
	// -----------------
	public function loadNodeWidgets() {

		if (empty($this->widgets)) return;

		// Get class and widgets module
		// -----------------------------
		$widgetsClass = \Core::getClass('widget');
		$widgetsModule = \Core::getModule('widgets');
		// Out each one
		// ------------
		foreach($this->widgets as $widget) {

			// Generic widgets without role
			// ----------------------------
			if (empty($widget['role'])) {

					// Get widget
					// ----------
					$widgetInstance = @ $widgetsModule->widgets[$widget['id']];

					// Or load new instance
					// ---------------------
					if (empty($widgetInstance)) {

						// Read wdget
						// ----------
						$widgetObject = $widgetsClass::findPK($widget['id']);
						if (empty($widgetObject)) continue;

						// Add widget to output
						// --------------------
						$widgetInstance = \Core::getModule('widgets')->getWidget($widgetObject);
						$widgetInstance->node = $this;

					}

					// Merge args
					// ----------
					if (!empty($widget['args'])) {
						$widgetInstance->args = array_merge($widgetInstance->args, $widget['args']);
					}

					// Merge options
					// -------------
					if (!empty($widget['options'])) {
						$widgetInstance->options = array_merge($widgetInstance->options, $widget['options']);
					}

					$widgetInstance->out();

			}

			// Widgets with roles
			// ------------------
			else {
				$widgetsClass = \Core::getClass('widget');
				$widgetObject = $widgetsClass::findPK($widget['id']);
				$widgetInstance = \Core::getModule('widgets')->getWidget($widgetObject);
				$widgetInstance->node = $this;
				$this->roleWidgets[$widget['role']] = $widgetInstance;
			}
		}

	}
	// Initialize node to work
	// ------------------------
	public function initializeNode() {}

	// Pass node
	// ---------
	public function passNode() {

		// Add id
		// ------
		$nodesList = & \Core::getModule('sites')->nodes;
		$nodesList[] = $this->_id;


		// Get params
		// ----------
		if (!empty($_REQUEST[$this->_id])) $this->params = $_REQUEST[$this->_id];

		// Load node widgets
		// -----------------
		$this->loadNodeWidgets();

		// Update node ID
		// --------------
		if (!empty($this->_id)) {
			\Core::getApplication()->data['nodeID'] = $this->_id;
		}

		// Switch theme
		// ------------
		if (!empty($this->options['page']['theme'])) {
			\Loader::importPackage(array('path' => 'themes/'.$this->options['page']['theme']));
			\Core::getModule('compiler')->compile();	
			\Extension::initComponents();
			
			\Core::getApplication()->data['page']['theme'] = $this->options['page']['theme'];
		}

		// Set langauge
		// ------------
		if (!empty($this->options['page']['language'])) {
			\Core::getApplication()->data['language'] = $this->options['page']['language'];
		}

		// Switch page template
		// -------------------
		if (!empty($this->options['page']['template'])) {
			\Core::getApplication()->data['page']['template'] = $this->options['page']['template'];
		}
          
		if (!empty($this->options['page']['htmlClasses'])) {
			\Core::getApplication()->data['page']['htmlClasses'] = $this->options['page']['htmlClasses'];	
		}
			
					        
		// Update title
		// --------------------
		if (!empty($this->title) && !empty($this->path) && @$this->isSystem !== true) {
		    \Core::getModule('sites')->appendTitle($this->title);

			// Update site node
			// ----------------
			\Core::getModule('breadcrumbs')->appendBreadcrumbs(array('id' => $this->_id, 'title' => $this->title, 'link' => $this->getURI()));
		}
		
		// SEO TITLE
		// ---------------
		if (!empty($this->seo['title'])) {
			\Core::getModule('sites')->setTitle($this->seo['title']);
		}


	}

	// Process path
	// ------------
	public function processPath($path, $pathIndex = 0) {

		// Set path
		// --------
		$this->currentPath = $path;
		$this->currentPathIndex = $pathIndex;
		$nextPathElement = @ $path[$pathIndex + 1];

		// Pass this
		// ---------
		$this->passNode();

		// Get children nodes
		// ------------------
		$nodesClass = \Core::getClass('node');
		$children = $nodesClass::find(array(
			'query' => array(
				'parent' => $this->_id,
				'hidden' => array('$ne' => true),
				'isSystem' => true
			)
		));

		// Process each system children node
		// ---------------------------------
		if (!empty($children)) {
			foreach($children as $child) {
											
				// Get and process
				// ---------------
				$childNode = \Node::getNodeObject($child);
				if ($childNode->isEnabled()) {
					$childNode->processPath($path, $pathIndex + 1);
				}
			}
		}

		// Execute if path is empty
		// -----------------------
		if (empty($nextPathElement)) {
			$this->executeNode();
			return;
		}

		//
		$children = $nodesClass::find(array(
			'query' => array(
				'parent' => $this->_id,
				'hidden' => array('$ne' => true),
				'isSystem' => array('$ne' => true),
				'$or' => array(
					array('path' => $nextPathElement),
					array('_id' => $nextPathElement)
				)
			)
		));


		// Process children node
		// ---------------------
		if (!empty($children)) {
			$node = \Node::getNodeObject($children[0]);
			$node->processPath($path, $pathIndex + 1);
		}

	}

	// Execute node code
	// -----------------
	public function executeNode() {

		// Set current
		// -----------
		\Core::getModule('sites')->currentNode = $this->_id;
		
		
		// SEO keywords
		// ------------
		if (!empty($this->seo['keywords'])) {
			\Core::getModule('seo')->setKeywords($this->seo['keywords']);
		}

		// SEO description
		// ---------------
		if (!empty($this->seo['description'])) {
			\Core::getModule('seo')->setDescription($this->seo['description']);        
		}
		
		// If there is a node alias and it is not equal to request uri, then do a redirect to the alias address 
		// ---------------
		if (!empty($this->seo['alias']) and $this->seo['alias'] != \Core::getModule('core')->application->request->uri) {
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.$this->seo['alias']);
			die();
		}
		
		// If node is renderable, render
		// -----------------------------
		$this->renderNode('full');
	}


	// Rener content of node
	// ---------------------
	public function render($mode = 'full', $options = array()) {

		// Render widget content
		// ----------------------
		$renderMethod = 'renderMode'.$mode;
		if (method_exists($this, $renderMethod)) {
			$content = call_user_func(array($this, $renderMethod), $options);
			return $content;
		}

		// No any content
		// --------------
		return false;

	}

	// Render resource
	// ---------------
	public function renderNode($mode = 'full', $options = array()) {

		// Append SEO elements
		// -------------------
		if ($mode == 'full') {

			// Append keywords
			// ---------------
			if (!empty($this->seo['keywords'])) {
				\Core::getModule('seo')->setKeywords($this->seo['keywords']);
			}

			// Add description
			// ---------------
			if (!empty($this->seo['description'])) {
				\Core::getModule('seo')->setDescription($this->seo['description']);
			}
		}
		
		// Content block
		// -------------
		$contentBlock = first_var(\Core::getModule('content')->getSetting('contentBlock'), 'content');

        $options = array('block' => $contentBlock);

        if (!empty($this->options['htmlClasses'])) {
            $options['htmlClasses'] = $this->options['htmlClasses'];
        }

		// Emit content
		// ------------
		$widget = \Core::getModule('widgets')->createWidget(array('node',
			array('node' => $this, 'mode' => $mode),
            $options
		));

		// Set node as current and out
		// ---------------------------
		$widget->node = $this;
		$widget->out();

		// Or we doesn't have render method
		// --------------------------------
		return false;
	}


	public function renderModeSearchResult() {
		$content = '<h3><a href="'.$this->getURI().'">'.$this->title.'</a></h3>';
		return $content;
	}
}
