<?php

namespace Core\DataViews;

// Отображение текста
// ------------------
class Field extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'field'
	);

	// Отображение
	// -----------
	public function execute() {

		$value = '';
		$type = first_var(@ $this->options['type'], 'text');

		// Get method
		// ----------
		if (method_exists($this, 'render'.$type)) {
			$value = call_user_func(array($this, 'render'.$type));
		}
		if (!empty($this->options['units'])) $value .= ' '.$this->options['units'];

		return $value;
	}

	// Output formatted text
	// ---------------------
	public function renderText() {
		return \DataView::get('text', $this->value, @ $this->options['displayOptions']);
	}

	// Output number
	// -------------
	public function renderNumber() {
		return \DataView::get('number', $this->value, @ $this->options['displayOptions']);
	}


	// Datetime
	// --------
	public function renderDatetime() {
		return \DataView::get('datetime', $this->value, @ $this->options['displayOptions']);
	}


	// Output an object representation	
	// -------------------------------
	public function renderObject() {

		// Get class value
		// ---------------
		if (!empty($this->options['class'])) {
			$class = @ $this->options['class'];
			$objectID = $this->value;
		}

		// From reference type
		// -------------------
		else if (is_array($this->value) && !empty($this->value['class'])) {
			$class = @ $this->value['class'];
			$objectID = @ $this->value['id'];
		}

		// If no any class was choosen, skip this
		// --------------------------------------
		if (empty($class) || empty($objectID)) return;

		// Load a class
		// ------------
		$classComponent = \Core::getComponent('class', $class);

		// Load an object
		// --------------
		$object = $classComponent::findPK($objectID);
		if (empty($object)) return;

		// Return an object's identity
		// ---------------------------
		return $object->getIdentity();

	}

	// Output a boolean value
	// ----------------------
	public function renderBoolean() {
		if ($this->value === true) return 'да';
		else if ($this->value === false) return 'нет';
		return '-';
	}

	// Output a select
	// ---------------
	public function renderSelect() {

		// If value isn't exists, return -
		// -------------------------------
		if (empty($this->value)) return '-';

		// If format values aren't exists, return value as is
		// ---------------------------------------------------
		if (empty($this->options['values']) || !isset($this->options['values'][$this->value])) return (string) $this->value;

		// Or we just out associated value
		// -------------------------------
		return @ $this->options['values'][$this->value];

	}

	// Output a multiselect
	// --------------------
	public function renderMultiselect($value = null, $format = array()) {
	}



}
