<?php

namespace Core\Objects\Widgets;

class ObjectEditor extends \Widget {

	// Component
	// ---------
	public static $component = array('id' => 'object-editor');

	// Actions list
	// ------------
	public function renderActions() {
	}

	// Render form
	// ------------
	public function renderForm() {

		// Collect fields
		// --------------
		$class = $this->args['class'];
		$classObject = @\Core::getComponent('class', $class);

		$format = $classObject::getEditorFormat();
		$blocks = $classObject::getEditorBlocks();

		// Set button title
		// ----------------
		$buttonTitle = 'Сохранить';
		if (empty($this->object)) $buttonTitle = 'Создать';

		// Generate data
		// -------------
		$data = @ $this->object->properties;
		if (empty($data)) $data = array();

		// Append data
		// -----------
		if (!empty($this->args['overrideValue'])) $data = array_merge($data, $this->args['overrideValue']);

		// Success URI
		// -----------
		if (!empty($this->args['successURI'])) $data['formSuccessURI'] = $this->args['successURI'];

		// Detect correct title
		// ---------------------
		if (!empty($this->args['title'])) $title = $this->args['title'];
		else {

			// If we have object, get identity
			// -------------------------------
			if (!empty($this->object->_id)) {
				$title = 'Редактирование объекта &laquo'.$this->object->getIdentityTitle().'&raquo;';
			}

			// Else, we create it
			// ------------------
			else {
				$classTitle = first_var(@ $classObject::$component['title'], @ $classObject::$component['id']);
				$title = 'Создание &laquo'.$classTitle.'&raquo';		
			}
		}

		$title = '<h3>'.$title.'</h3>';
		                      	
		// Render form
		// -----------
		return $title. \Widgets::get('form', array(

			'format' => $format,
			'blocks' => $blocks,

			'template' => @ $this->args['template'],
			// Buttons
			// -------
			'buttons' => array(
				array('id' => 'save', 'type' => 'submit', 'title' => $buttonTitle)
			),
			'value' => $data,

			// URIs
			// ----
			'actionURI' => '/module/objects-control/save/'.$this->args['class'],
			'validatorURI' => '/module/objects-control/validate/'.$this->args['class']
		));
	}

	// Render
	// ------
	public function render() {

		// Read class
		// ----------
		$this->class = @ \Core::getComponent('class', $this->args['class']);
		if (empty($this->class)) return;
		$class = $this->class;

		// Read an object
		// --------------
		if (!empty($this->args['id'])) {
			$this->object = $class::findPK($this->args['id']);
		}		

		// Collect content here
		// --------------------
		$content =
			$this->renderActions().
			$this->renderForm();

		// Return content
		// --------------
		return $content;
	}
}