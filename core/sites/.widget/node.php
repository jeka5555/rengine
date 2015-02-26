<?php

namespace Core\Widgets;

class Node extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'id' => 'node',
		'editable' => true,
		'title' => 'Вывод узла',
	);

	// Here we keep list of rendered properties for this widget
	// --------------------------------------------------------
	public $renderedProperties = array();
	public $node = null; // Resource


	// Get widget args format
	// ----------------------
	public function getWidgetArgsFormat() {
		return array(
			'mode' => array('type' => 'text', 'title' => 'Режим отображения'),
			'id' => array('type' => 'object', 'class' => 'node', 'title' => 'Узел для вывода')
		);
	}

    // wrap
    // ----------------
    public function wrap($content) {
        if (!empty($this->node->options['tag'])) $this->options['tag'] = $this->node->options['tag'];
        $content = parent::wrap($content);
        return $content;
    }

	// Generate classes
	// ----------------
	public function generateHtmlClasses() {
        if (!empty($this->node->options['htmlClasses'])) $this->options['htmlClasses'] = $this->node->options['htmlClasses'];
		$classes = parent::generateHtmlClasses();
		return $classes;
	}

	// Generate HTML attributes
	// ------------------------
	public function getHTMLAttributes() {

		$attributes = parent::getHTMLAttributes();

		// Add resource data
		// -----------------
		if (!empty($this->node->type)) $attributes['data-node-type'] = @ $this->node->type;
		if (!empty($this->node->_id)) $attributes['data-node-id'] = @ $this->node->_id;
		if (!empty($this->mode)) $attributes['data-node-mode'] = $this->mode;
		if (!empty($this->node->path)) $attributes['data-node-path'] = $this->node->path;

		return $attributes;

	}

	// Script
	// ------
	public function addControllerScript() {

		// General controller
		// ------------------
		parent::addControllerScript();

		// Can edit
		// --------
		$editorUser = \Rules::check(array(
			array('type' => 'or', 'rules' => array(
				array('type' => 'userRole', 'role' => 'administrator'),
				array('type' => 'userRole', 'role' => 'super'),
				array('type' => 'userRole', 'role' => 'content-manager'),
			))
		));

		// Attach only for edit mode
		// -------------------------
		if (@ \Core::getApplication()->data['editMode'] !== true || empty($this->node->_id) || !$editorUser) return false;

		// This widget control an object
		// -----------------------------
		if (!empty($this->node->_id)) {
			$this->attachObject(array('class' => 'node', 'id' => $this->node->_id));
		}

	}

	// Render node
	// ---------------
	public function render() {

		// Select mode
		// -----------
		$this->mode = first_var(@ $this->args['mode'], 'full');


		// Read resource
		// -------------
		if (!empty($this->args['id'])) {
			$nodeClass = \Core::getClass('node');
			$nodeObject = $nodeClass::findPK($this->args['id']);
			$this->node = \Node::getNodeObject($nodeObject);
		}
		else if (!empty($this->args['node'])) {
			$this->node = $this->args['node'];
		}

		// If nothing found, return
		// ------------------------
		if (empty($this->node)) {
			$this->cancel();
			return false;
		}
		// Get widget id
		// -------------
		$this->node->index = $this->index;

        if (method_exists($this->node, 'addNodeControllerScript')) {
            $this->args['widgetHtmlID'] = $this->generateHtmlID();
            call_user_func(array($this->node, 'addNodeControllerScript'), $this->args);
        }

		// Render in mode
		// --------------
		$result = $this->node->render($this->mode, $this->args);

		if ($result === false) $this->cancel();

		return $result;

	}

}
