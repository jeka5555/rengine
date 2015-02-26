<?php

class Type extends \Component {

	// Init component
	// --------------
	public static $component = array(
		'id' => 'type',
		'type' => 'component',
		'autoload' => true,
		'title' => 'Тип данныех'
	);

	// Variables
	// ---------
	public $value = null; // Value by default


	// Format value
	// ------------
	public function format() {
		return $this->value;
	}

	// Validate value
	// --------------
	public function validate() {
		return true;
	}

	// Get value
	// ---------
	public function getTextValue() {
		return $this->value;
	}

	// Get HTML value
	// --------------
	public function getHTMLValue() {
		return '<div><strong>'.$this->title.'</strong> '.$this->getTextValue().'</div>';
	}

	// Render input
	// ------------
	public function renderHTMLInput() {
	}


}