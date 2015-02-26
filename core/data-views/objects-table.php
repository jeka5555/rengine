<?php

namespace Core\DataViews;

// Изображение
// ------------------
class ObjectsTable extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'objects-table'
	);

	// Get info for class fields
	// -------------------------
	public function getTableFields() {

		$fields = array();

		// If fields are given, take it
		// ----------------------------
		if (!empty($this->options['fields'])) $fields = $this->options['fields'];
		return $fields;
	
	}


	// Render table headers
	// --------------------
	public function renderHeading() {
	
		$content = '';
		foreach ($this->options['fields'] as $field) {
				$content .= '<div>'.$field.'</div>';
		}

		// Return heading content
		// ----------------------
		return '<div class="core-table-heading">'.$content.'</div>';
	}


	// Render result
	// -------------
	public function renderResult() {

		// We need real fields list
		// ------------------------
		if (!is_array($this->fields)) return;

		// Get objects
		// -----------
		$objectsClass = $this->class;
		$objects = $objectsClass::find();

		// Render items
		// ------------
		$content = '';
		foreach($objects as $object) {
			$content = $this->renderRow($object);
		}
		return $content;
	
	}

	// Render one table row
	// --------------------
	public function renderRow($object = array()) {

		$content = '';

		// Itterate over all fields
		// ------------------------
		foreach ($this->fields as $field) {

			// Get value and wrap in to div
			// ----------------------------
			$fieldID = $field['id'];
			$value = @ $object->get($fieldID);

			$content .= '<div>'.$this->renderCell($value, $field).'</div>';
		}

		// Return complete row
		// -------------------
		return '<div class="core-table-row">'.$content.'</div>';
	}

	// Format one cell to fit table's requirements
	// -------------------------------------------
	public function renderCell($value = null, $format = array()) {

		// Try to detect a type
		// --------------------
		$type = 'text';

		// If format isn't array, just return value
		// ----------------------------------------
		if (!is_array($format)) return (string)$value;

		// If we have formatter function. defined, use it
		// ----------------------------------------------
		if (!empty($format['formatFunction'])) {
			return $this->formatWithFunction($value, $format);
		}

		// Detect if formatter function exists
		// -----------------------------------
		$formatFunction = 'formatWith'.$type;
		if (!method_exists($this, $formatFuncton)) return;

		// Use function to format an result
		// --------------------------------
		return call_user_func(array($this, $formatFunction), $value, $format);
	}


	// Output value formatted with function
	// ------------------------------------
	public function formatWithFunction($value = null, $format = array()) {
		return call_user_func($format['formatFunction'], $value, $format);
	}



	// Отображение
	// -----------
	public function execute() {

		// Require class
		// -------------
		if (empty($this->options['class'])) return;
		$this->class = @ \Core::getComponent('class', $this->options['class']);
		if (empty($this->class)) return;

		$class = $this->class;

		// Get fields list
		// ---------------
		$this->fields = $this->getTableFields();

		// Output table
		// ------------
		$content ='<div class="core-table">
			'.$this->renderHeading().'
			'.$this->renderResult().'
		</div>';

		return $content;

	}


}
