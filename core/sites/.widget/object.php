<?php

namespace Core\Widgets;

class Object extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'id' => 'object',
		'editable' => true,
		'title' => 'Вывод объекта',
	);

	// Here we keep list of rendered properties for this widget
	// --------------------------------------------------------
	public $renderedProperties = array();
	public $node = null; // Resource


	// Get widget args format
	// ----------------------
	public function getWidgetArgsFormat() {
		return array(
			'object' => array('type' => 'object', 'title' => 'Объект'),
			'mode' => array('type' => 'text', 'title' => 'Режим отображения'),
		);
	}

	// Generate classes
	// ----------------
	public function generateHtmlClasses() {
		if (!empty($this->args['object']->options['htmlClasses'])) $this->options['htmlClasses'] = $this->args['object']->options['htmlClasses'];
		$classes = parent::generateHtmlClasses();
		return $classes;
	}

	// Generate HTML attributes
	// ------------------------
	public function getHTMLAttributes() {

		$attributes = parent::getHTMLAttributes();

		$this->object->class = $this->object->getClassID();
		
		// Add resource data
		// -----------------
		if (!empty($this->object->class)) $attributes['data-object-class'] = @ $this->object->class;
		if (!empty($this->object->_id)) $attributes['data-object-id'] = @ $this->object->_id;
		if (!empty($this->mode)) $attributes['data-object-mode'] = $this->mode;

		if (!empty($this->args['object']->options['htmlAttributes']) and is_array($this->args['object']->options['htmlAttributes'])) $attributes = array_merge($attributes, $this->args['object']->options['htmlAttributes']);

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
		if (@ \Core::getApplication()->data['editMode'] !== true || empty($this->object->_id) || !$editorUser) return false;

		// This widget control an object
		// -----------------------------
		if (!empty($this->object->_id)) {
			$this->attachObject(array('class' => $this->object->getClassID(), 'id' => $this->object->_id));
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
		if (!empty($this->args['id']) && !empty($this->args['class'])) {
			$objectClass = \Core::getClass($this->args['class']);
			$this->object = $objectClass::findPK($this->args['id']);
		}
		else if (!empty($this->args['object'])) {
			$this->object = $this->args['object'];
		}

		// If nothing found, return
		// ------------------------
		if (empty($this->object)) {
			$this->cancel();
			return false;
		}
		// Get widget id
		// -------------
		$this->object->index = $this->index;


        if (method_exists($this->object, 'addObjectControllerScript')) {
            $this->args['widgetHtmlID'] = $this->generateHtmlID();
            call_user_func(array($this->object, 'addObjectControllerScript'), $this->args);
        }

		// Render in mode
		// --------------
		$result = $this->object->render($this->mode);

		if ($result === false) $this->cancel();

		return $result;

	}

}
