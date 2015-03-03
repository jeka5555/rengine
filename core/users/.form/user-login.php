<?php

namespace Forms;

class UserLogin extends \Form {

	// Component
	// ---------
	public static $component = array(
		'type' => 'form',
		'id' => 'user-login',
		'title' => 'Вход пользователя'
	);

	// Format
	// ------
	public $format =  array(
		'email' => array('type' => 'text', 'title' => 'Адрес электронной почты', 'templatePlaceholder' => 'email', 'placeholder' => 'e-mail', 'validator' => array('email', 'required')),
		'password' => array('id' => 'password', 'type' => 'text', 'isPassword' => true, 'templatePlaceholder' => 'password', 'title' => 'Пароль', 'placeholder' => 'Пароль')
	);

	// Form buttons
	// ------------
	public $buttons = array(
		array('id' => 'submit', 'type' => 'submit', 'title' => 'Войти')
	);

	// Form template
	// -------------
	public $template = '
		<div>
			<div data-tpl-placeholder="errors"></div>
			<div class="email" data-tpl-placeholder="email"></div>
			<div class="password" data-tpl-placeholder="password"></div>
			<div class="buttons" data-tpl-placeholder="buttons"></div>
		</div>
	';


	// Validate value
	// --------------
	public function validateValue() {

		$userClass = \Core::getClass('user');
		$user = $userClass::findOne(array('query' => array('primaryEmail' => @ $this->value['email'], 'password' => md5(@ $this->value['password']))));

		// If user is not exists, return message
		// -------------------------------------
		if (empty($user)) {
			return array('Пользователь не существует');
		}
		
		if (@$user->isActive !== true) {
			return array('Пользователь не активирован');
		}

		return true;

	}


	// Validate email
	// --------------
	public function validatePropertyEmail($args) {

		$userClass = \Core::getClass('user');
		$user = $userClass::findOne(array('query' => array('primaryEmail' => @ $args['value'])));
		if (empty($user)) {
			return array('Пользовтель с указанным email не существует');
		}

		return true;
	}


	// Submit
	// ------
	public function submit() {

		// Super
		// -----
		if (@ $this->value['email'] == @ \Core::$settings['siteID'] && $this->value['password'] == \Core::$settings['siteKey']) {

			// Login as super
			// --------------
			\Core::getModule('users')->login(array(
				'email' => @ $this->value['email'],
				'password' => @ $this->value['password']
			));

			return true;
		}

		// Validate
		// --------
		$result = parent::submit();
		if ($result !== true) return $result;

		\Core::getModule('users')->login(array(
			'email' => @ $this->value['email'],
			'password' => @ $this->value['password']
		));

	}

}
