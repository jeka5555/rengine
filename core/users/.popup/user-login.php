<?php

namespace Core\Users\Popups;


class UserLogin extends \Core\Components\Popup {

	// Component registration
	// ----------------------
	public static $component = array('id' => 'user-login');

	// Data
	// ----
	public $title = 'Вход пользователя';
	public $class = 'dialog';
	public $buttons = null;

	// Get content
	// -----------
	public function getContent() {

		// If user is logged in, give them logout form
		// -------------------  ------------------------
		if (\Rules::checkRule(array('type' => 'userLogged'))) {
			$content = \Widgets::get('form', array('id' => 'user-logout'));
		}

		// If user isnt' logged in, push form
		// ----------------------------------
		else {
			$content = \Widgets::get('form', array('id' => 'user-login'));
		}


		return $content;
	}
}
