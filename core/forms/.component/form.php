<?php

class Form extends \Component {


	public static $component = array(
		'type' => 'component',
		'id' => 'form',
		'title' => 'Форма',
		'autoload' => true
	);



	// Render mail content
	// -------------------
	public function renderMailContent() {


		// Get form title
		// --------------
		$formTitle = first_var($this->title, static::$component['title'], static::$component['id']);


		$content = '';

		// Get format
		// ----------
		$format = $this->format;
		if (!empty($format)) {

			// Add each property
			// -----------------
			foreach($format as $propertyID => $property) {

				// Get title
				// ---------
				$title = first_var(@ $property['title'], $propertyID);

				// Add value
				// ---------
				switch(@ $property['type']) {

					// Boolean
					// -------
					case 'boolean':
						$value = @ $this->value[$propertyID];
						if ($value === true) $value = 'да';
						else if ($value === false) $value = 'нет';
						else $value = 'не задано';
						break;

					// Select
					// ------
					case 'select':
						$selectValue = @ $property['values'][$this->value[$propertyID]];
						if (!empty($selectValue)) $value = $selectValue;
						else $value = 'не задано';
						break;

					// Datetime
					// --------
					case 'datetime':
						$value = date("Y-m-d H:i:s", @ $this->value[$propertyID]);
						break;

					// Multiselect
					// -----------
					case 'multiselect':

						// Get value
						// ----------
						$selectValue = @ $this->value[$propertyID];

						// If value is empty, do nothing
						// -----------------------------
						if (empty($selectValue)) $value = 'ничего не выбрано';

						// Or parse
						// --------
						else {
							$valuesList = array();

							// Itterate each value to get complete list
							// ----------------------------------------
							foreach($selectValue as $val) {

								// If value exists, add to list
								// ----------------------------
								if (!empty($property['values'][$val])) {
									$valuesList[] = $property['values'][$val];
								}
							}

							$value = join(', ', $valuesList);
						}
						break;



					// Default
					default:
						$value = @ $this->value[$propertyID];
						break;
				}


				// Add line to content
				// -------------------
				$content .= '<div class="property"><strong>'.$title.':</strong> '.$value.'</div>';
			}
		}

		// Add info
		// --------
		$content = '
			<div>С сайта '.$_SERVER['HTTP_HOST'].' была отправлена форма "'.$formTitle.'"</div>
			<div>Время отправки: '.date("Y-m-d H:i:s", time()).'</div>
			<div>IP-адрес отпрвителя: '.$_SERVER['REMOTE_ADDR'].'</div>
			<br/>
			'.$content.'
		';


		// Return content
		// --------------
		return $content;


	}

	// Email form
	// ----------
	public function mailTo($address = null) {

		// Admin email
		// -----------
		if (empty($address)) {
			if (!empty($this->submitEmail)) $address = $this->submitEmail;
			else $address = \Core::getModule('core')->getSetting('adminEmail');
		}

		// Have email?
		// -----------
		if (empty($address)) return;
		
		// From email
		$fromAddress = first_var(@ \Core::getModule('core')->getSetting('fromEmail'), 'no-reply@'.$_SERVER['HTTP_HOST']);

		// Get form title
		// --------------
		$formTitle = first_var(@ $this->title, @static::$component['title']);

		// Send email
		// ----------
		$emailModule = \Core::getModule('email');
		$emailModule->send(array(
			'to' => $address,
			'from' => $fromAddress,
			'task' => 'Отправка формы "'. $formTitle .'"',
			'message' => $this->renderMailContent()
		));


		// True
		// ----
		return true;

	}

	// Generic form validation
	// -----------------------
	public function validate() {

		// Validation result
		// -----------------
		$result = array();

		// Check properties
		// ----------------
		if (is_array($this->format))
		foreach($this->format as $propertyID => $property) {

			// Collect errors here
			// ------------------
			$errors = array();

			// Any validators?
			// ---------------
			if (!empty($property['validator'])) {

				// Single line validator
				// ---------------------
				if (is_string($property['validator'])) {

					// Get result
					// ----------
					$validatorResult = $this->runValidator($property['validator'], @ $this->value[$propertyID], $property);

					// Append errors
					// -------------
					if ($validatorResult !== true) {
						$errors = array_merge($errors, $validatorResult);
					}
				}

				// Complex validator
				// -----------------
				else if (is_array($property['validator'])) {
					foreach ($property['validator'] as $validator) {

						// Itterate each
						// -------------
						$validatorResult  = $this->runValidator($validator, $this->value[$propertyID], $property);

						// Add errors
						// ----------
						if ($validatorResult !== true) {
							$errors = array_merge($errors, $validatorResult);
						}

					}
				}
			}

			// Method
			// ------
			$validatorMethod = 'property'.$propertyID;
			if (method_exists($this, 'validate'.$validatorMethod)) {
				$validatorResult = $this->runValidator($validatorMethod, $this->value[$propertyID], $property);
				if ($validatorResult !== true) {
					$errors = array_merge($errors, $validatorResult);
				}
			}


			// Add errors to array
			// -------------------
			if (!empty($errors) && is_array($errors)) {
				$result['inputs'][$propertyID] = $errors;
			}

		}

		// Validate value
		// --------------
		$valueResult = $this->validateValue();
		if ($valueResult !== true) {
			$result['form'] = $valueResult;
		}

		// Результат
		// ---------
		if (empty($result)) return true;
		return $result;
	}

	// Validate whole form value
	// --------------------------
	public function validateValue() { return true; }

	// Запус одного валидатора
	// -----------------------
	public function runValidator($validator, $value = null, $field = null) {

		// Определяем валидаторы
		// ---------------------
		$validatorType = null;
		if (is_string($validator)) $validatorType = $validator;
		else if (is_array($validator) && isset($validator['type']) && is_string($validator['type'])) $validatorType = $validator['type'];

		// Если не нашли тип, то ок
		// ------------------------
		if ($validatorType == null) return true;

		// Проверка наличия валидатора
		// ---------------------------
		if (method_exists($this, 'validate'.$validatorType)) {

			// Call validator method
			// ---------------------
			$validatorResult = call_user_func(
				array($this, 'validate'.$validatorType),
				array(
					'value' => @ $value,
					'property' => @ $field,
					'validator' => $validator
				)
			);

			// Если вернули что-то, то добавляем как ошибку
			// --------------------------------------------
			if (!empty($validatorResult) && $validatorResult !== true) {
				if (is_array($validator) && !empty($validator['errors'])) return $validator['errors'];
				else return $validatorResult;
			}
		}

		// By default validator expired to ok
		// ----------------------------------
		return true;

	}

	// Валидатор required
	// ------------------
	public function validateCompare($args = array()) {

		// Take value to compare
		// ---------------------
		$compareField = @$args['validator']['property'];

		if (!empty($compareField)) {
			if ($this->value[$compareField] !== $args['value']) {
				return first_var(@$args['validator']['errors'], array('Поле не совпадает'));
			}
		}
	
		return true;
	}

	// Валидатор required
	// ------------------
	public function validateRequired($args = array()) {
		if (empty($args['value'])) return array('Поле не должно быть пустым');
		return true;
	}

	// Валидатор email
	// ---------------
	public function validateEmail($args = array()) {
		if (!empty($args['value']) and !filter_var($args['value'], FILTER_VALIDATE_EMAIL)) return array('Поле должно иметь формат email');
		return true;
	}

	// Submit form process
	// -------------------
	public function submit() {

		// Submit validation
		// -----------------
		$validationResult = $this->validate();
		if ($validationResult !== true) return array('errors' => $validationResult);

		// All is OK
		// ---------
		return true;

	}


}
