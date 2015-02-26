<?php

namespace Forms;

class UserChangePassword extends \Form {

		// Component
		// ---------
		public static $component = array(
			'type' => 'form',
			'id' => 'user-change-password',
			'title' => 'Форма изменения пароля'
		);

		// Поля формы
		// ----------
		public static $form = array(
			'format' => array(
				array('id' => 'password', 'type' => 'text', 'isPassword' => true, 'title' => 'Пароль', 'placeholder' => 'введите пароль'),
				array('id' => 'passwordVerify', 'type' => 'text', 'isPassword' => true, 'title' => 'повторите пароль'),
			),
			'buttons' => array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Изменить')),
			'actionURI' => '/module/ran-users/change-password'
		);
}