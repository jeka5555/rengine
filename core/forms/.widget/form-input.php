<?php

namespace Widgets;

class FormInput extends \Widget {

	public $properties = array();

	// Component
	// ---------
	public static $component = array(
		'id' => 'form-input',
		'title' => 'Поле формы'
	);

	// Get widget args format
	// ----------------------
	public function getWidgetArgsFormat() {
		return array(
			// Primary form ID
			// -----------------
			'type' => array('type' => 'text', 'title' => 'Тип поля'),
		);
	}

    // Add widget controller
    // ---------------------
    public function addControllerScript() {

        parent::addControllerScript();

        // Get ID
        // ------
        $htmlID = $this->generateHtmlID();

        // Get script properties
        // ---------------------
        $controllerArgs = array(
            'widget' => '#'.$htmlID,
            'id' => @$this->args['id'],
            'format' => @$this->args['format']
        );

        \Events::send('addEndScript', '
            formInput'.ucwords(@$this->args['id']).' = new UI.FormInputs.'.@$this->args['input'].'('.json_encode($controllerArgs).');
        ');

    }


	// Render form
	// -----------
	public function render() {

	}

}