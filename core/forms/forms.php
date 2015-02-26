<?php

namespace Modules;

class Forms extends \Module {

	// Информация компонента
	// ---------------------
	public static $component = array(
		'id' => 'forms',
		'title' => 'Формы'
	);


	// Check form validation
	// ---------------------
	public function actionValidate($data, $action) {

		// Get form id
		// -----------
		$formID = @ $action[3];
		if (empty($formID)) return false;

		// Create instance
		// ---------------
		$form = \Core::getComponent('form', $formID);
		if (empty($form)) return false;

		$formInstance = $form::getInstance();
		$formInstance->value = $data;

		// Validate
		// --------
		return $formInstance->validate();


	}

	// Отправка формы
	// --------------
	public function actionSubmit($data, $action) {

		// Get form id
		// -----------
		$formID = @ $action[3];
		if (empty($formID)) return;

		// Create instance
		// ---------------
		$form = \Core::getComponent('form', $formID);
		if (empty($form)) return;

		// Set form data
		// -------------
		$formInstance = $form::getInstance();
		$formInstance->value = $data;

		// Submit
		// ------
		return $formInstance->submit();

	}

	// Фильтрация полей по доступу текущего пользоватлея
	// -------------------------------------------------
	public static function filterFields($fields = array()) {

		foreach($fields as $fieldKey => $field) {
			if (!empty($field['access'])) {
				if (!\Rules::check($field['access'])) {
					unset($fields[$fieldKey]);
				}
			}
		}
		return $fields;
	}

}