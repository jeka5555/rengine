<?php

namespace Forms;

class UserRegisterConfirm extends \Form {

	// Component
	// ---------
	public static $component = array(
		'type' => 'form',
		'id' => 'user-confirm-registration'
	);

	// Form data
	// ---------
	public $format = array(
		'key' => array('type' => 'text', 'title' => 'Ключ'),
	);

	public $buttons = array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Подтвердить'));

	// Validate
	// --------
	public function validatePropertyKey($args = array()) {

		// Check for emptyness
		// -------------------
		if (empty($args['value'])) return array('Необходимо ввести не пустое значение');

		// Get user with this key
		// ----------------------
		$userClass = \Core::getComponent('class', 'user');
		$user = $userClass::findOne(array('query' => array('confirmationKey' => (int)$args['value'])));

		// If no user, notify
		// ------------------
		if (empty($user)) {
			return array('Введенный ключ не существует. Попробуйте снова');
		}

		if ($user->isActive == true) {
			return array('Профиль уже активирован, пройдите на <a href="http://oil-traders.ru/login">страницу входа</a>');
		}                                          

		return true;

	}
	
	// Submit form
	// -----------
	public function submit() {
		
		// Validate
		// --------
		$result = parent::submit();
		if ($result !== true) return $result;
		
		if (empty($this->value['key'])) return false;

		// Get user with this key
		// ----------------------
		$userClass = \Core::getClass('user');
		$user = $userClass::findOne(array('query' => array('confirmationKey' => (int)$this->value['key'])));

		if (empty($user)) return false;
		
		// Activate user
		// -------------
		$user->isActive = true;
		$user->save();
		
		\Events::send('setLocation', '/users/login');
		
	}

}