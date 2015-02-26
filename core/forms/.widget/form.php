<?php

namespace Widgets;

class Form extends \Widget {

	public $properties = array();

	// Component
	// ---------
	public static $component = array(
		'id' => 'form',
		'title' => 'Форма'
	);

	// Get widget args format
	// ----------------------
	public function getWidgetArgsFormat() {
		return array(

			// Primary form ID
			// -----------------
			'id' => array('type' => 'component', 'title' => 'Какая форма выводиться?', 'componentType' => 'form'),

			// Look and control
			// -----------------
			'title' => array('type' => 'text', 'title' => 'Название формы'),
			'template' => array('type' => 'text', 'title' => 'Имя шаблона для вывода формы'),
			'controller' => array('type' => 'text', 'title' => 'Контроллер формы'),
			'jsClass' => array('type' => 'text', 'title' => 'Класс формы'),

			// Form elements
			// -------------
			'format' => array('type' => 'record', 'title' => 'Поля формы'),
			'additionalFields' => array('type' => 'record', 'title' => 'Дополнительные поля'),
			'data' => array('type' => 'record', 'title' => 'Данные формы', 'extendable' => true),
			'buttons' => array('type' => 'record', 'title' => 'Кнопки формы'),

			// URI's
			// -----
			'submitURI' => array('type' => 'text', 'title' => 'Обработчик'),
		);
	}


	// Render form
	// -----------
	public function render() {

		// Create form class
		// -----------------
		if (!empty($this->args['id'])) {

			$formComponent = \Core::getComponent('form', $this->args['id']);

			if (!empty($formComponent)) {
				$form = $formComponent::getInstance();
			}
		}

		// If empty, create default one
		// ----------------------------
		if (empty($form)) {
			$form = \Form::getInstance();
		}

		// Override form data
		// ------------------
		if (!empty($this->args['id'])) $form->id = $this->args['id'];
		if (!empty($this->args['title'])) $form->title = $this->args['title'];
		if (!empty($this->args['template'])) {$form->template = $this->args['template']; }
		if (!empty($this->args['format'])) {$form->format = $this->args['format']; }
		if (!empty($this->args['buttons'])) { $form->buttons = $this->args['buttons']; }

		// Set form properties
		// -------------------
		if (!empty($this->args['data'])) $form->data = $this->args['data'];

		// URI
		// ---
		if (!empty($this->args['submitURI'])) { $form->submitURI = $this->args['submitURI']; }

		// Form data
		// ---------
		if (!empty($this->args['value'])) { $form->value = $this->args['value']; }

		// Get ID's
		// --------
		$formID = first_var(@ $this->args['id'], 'form'.uniqid());
		$widgetID = first_var(@$this->args['widgetID'], @$form->widgetID, 'form'.uniqid());
		$formClass = first_var(@ $formData['formClass'], 'UI.Form');

		// Submit URI
		// ----------
		if (empty($form->submitURI) && !empty($this->args['id'])) {
			$form->submitURI = '/module/forms/submit/'.$this->args['id'];
		}


		// Build a form data
		// -----------------
		$formArgs = array(

			// IDs
			// ---
			'formID' => $this->args['id'],
			'title' => $form->title,
			'widgetID' => '#'.$widgetID,

			// Format
			// ------
			'format' => $form->format,
			'buttons' => $form->buttons,

			// Options
			// -------
			'showInputLabels' => $form->showInputLabels,
			'showInputID' => $form->showInputID,
			'showInputHints' => $form->showInputHints,

			// Data
			// ----
			'object' => $form->value,

			'template' => $form->template,
			'submitURI' => $form->submitURI,

			'controller' => @$form->controller
		);

		// Create form widget
		// ------------------
		$content = '<div class="core-js-form" id="'.$widgetID.'"></div>';

		// Add form script
		// ---------------
		\Events::send('addEndScript', ''.$widgetID.' = new '.$formClass.'('.json_encode($formArgs).');', 'end');

		// Get
		// ---
		return $content;

	}

}