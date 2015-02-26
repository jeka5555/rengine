<?php

namespace Core\Users;

class UserRegistration extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'user-registration',
		'title' => 'Страница регистрации пользователей'
	);

	// Process path
	// ------------
	public function processPath($path, $pathIndex = 0) {

		// Pass node
		// ---------
		$this->passNode();

		// See what action
		// ---------------
		$action = @ $path[$pathIndex + 1];
		switch ($action) {

			// User login form
			// ---------------
			case 'login':
				$this->showLogin();
				break;  

			// Register user
			// -------------
			case 'register':
				$this->showRegister();
				break;

			// Recover password
			// ----------------
			case 'recover-password':
				$this->showRecoverPassword();
				break;

                // New password
                // ----------------
            case 'new-password':
				$this->showNewPassword();
				break;


		}

	}
	

	// Show login
	// ----------
	public function showLogin() {
		$loginFormWidget = \Core::getModule('widgets')->createWidget(array('form', array('id' => 'user-login'), array('block' => 'content')));
		$loginFormWidget->out();
	}
	
	// Show register
	// -------------
	public function showRegister() {
		$registerFormWidget = \Core::getModule('widgets')->createWidget(array('form', array('id' => 'user-register'), array('block' => 'content')));
		$registerFormWidget->out();
	}		
	
	// Show show recover
    // -----------------
    public function showRecoverPassword() {
        $recoverPasswordFormWidget = \Core::getModule('widgets')->createWidget(array('form', array('id' => 'user-recover-password'), array('block' => 'content')));
        $recoverPasswordFormWidget->out();
    }

    // Show new pass
    // -----------------
    public function showNewPassword() {
        $newPasswordFormWidget = \Core::getModule('widgets')->createWidget(array('form', array('id' => 'user-new-password'), array('block' => 'content')));
        $newPasswordFormWidget->out();
    }

}
