<?php

namespace Forms;

class UserRegister extends \Form {

	// Регистрация компонента	
	// ----------------------
	public static $component = array(
		'type' => 'form',
		'id' => 'user-register',
		'title' => 'Регистрация в системе'
	);

	// Информация о форме
	// -------------------
	public $format = array(

			// Данные имени
			// ------------
			array('id' => 'lastName', 'type' => 'text', 'title' => 'Фамилия', 'validator' => 'required'),
			array('id' => 'firstName', 'type' =>'text', 'title' => 'Имя', 'validator' => 'required'),
			array('id' => 'middleName', 'type' => 'text', 'title' => 'Отчество'),

			// Информация о регистрации
			// ------------------------
			array('id' => 'email', 'type' => 'text', 'title' => 'E-mail', 'hint' => 'Емейл используемый для подтверждения регистрации и для входа в систему', 'validator' => array('email', 'required', 'emailNotExists')),
			array('id' => 'password', 'type' => 'text', 'isPassword' => true, 'title' => 'Пароль', 'validator' => array('strongPassword', array('type' => 'compare', 'field' => 'passwordConfirmation', 'errors' => array('Пароль должен совпадать с проверочным занчением')))),
			array('id' => 'passwordConfirmation', 'type' => 'text', 'isPassword' => true, 'title' => 'Повтор пароля'),

			// Agreement
			// ---------
			array('id' => 'agreed', 'type' => 'boolean', 'title' => '<div class="registration-text">Я подтверждаю что согласен с <a href="javascript:openUserAgreement()">пользовательским соглашением</a></div>', 'validator' => array('type' => 'required', 'errors' => array('Вы должны принять пользовательское соглашение'))),			
	);

	public $buttons = array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Зарегистрироваться'));
	public $actionURI = '/module/ran-users/register';


	// Проверка пароля
	// ---------------
	public function validatorFieldPassword($args) {	
		$errors = array();
		if (empty($args['value'])) $errors[] = 'Пароль не должен быть пустым';
		if ($this->value['password'] != $this->value['passwordConfirmation']) $errors[] = 'Пароль не совпадает с проверочным значением';
		return $errors;
	}

	// Email must not exists
	// ---------------------
	public function validatorEmailNotExists($args) {
		$usersClass = \Core::getComponent('class', 'user');
		$usersWithEmail = $usersClass::findOne(array('query' => array('primaryEmail' => @ $args['value'])));
		if (!empty($usersWithEmail)) return array('Указанный адрес электронной почты уже существует');
	}
}