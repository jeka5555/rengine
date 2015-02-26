<?php

namespace Widgets;

// Отображение формы входа
// -----------------------
class LoginForm extends \Widget {

	// Информация компонента
	// ---------------------
	public static $component = array(
		'type' => 'widget',
		'id' => 'loginForm',
		'title' => 'Форма входа в систему',
		'module' => 'users'
	);

	private function renderLogin() {

		$module = \Core::getModule('users');

		$tabs = array();

		// Форма логина
		// ------------
		$loginForm = \Widgets::get('form', array('formID' => 'login'));
		$tabs[] = array(
			'id' => 'login',
			'title' => 'Авторизация',
			'content' => '<div class="user-login-form">'.$loginForm.'</div>'
		);

		// Для режима регистрации
		// ----------------------		
		if (@$module->settings['registration-enabled'] == true) {
			  $registerForm = new \Widgets\Form(array('formID' => 'register'));	
				$tabs[] = array(
					'id' => 'register',
					'title' => 'Регистрация',
					'content' =>'<div class="user-register-form">'.$registerForm->render().'</div>'
				);
			}

		// Выдаем табулированную форму
		// --------------------
		if (count($tabs) > 1) $content = \Widgets::get('tabs', array('tabs' => $tabs));  		
		else $content = $tabs[0]['content'];
		return $content;

	}

	private function renderLogout() {

			// Простая форма для выхода
			// ------------------------
			$logoutFormWidget = \Widgets::get('form', array( 
				'id' => 'logout',
				'buttons' => array(array('id' => 'submit', 'type' => 'submit', 'title' => 'Выход')),
				'actionURI' => '/module/users/logout'
			));

			// Информация о пользователе и кнопка выхода
			// -----------------------------------------
			$content = '<div class="user-info">Вы вошли как: <strong>'. @ \Core::getModule('users')->user->fullName. '</strong></div>'.
			'<div class="user-logout-form">'.$logoutFormWidget.'</div>';
			
			// Вывод контента
			// --------------
			return $content;

	}


	// Визуализация
	// ------------
	public function render() {


		// Не выводить, если логин запрещен
		// --------------------------------
		if (@ $this->componentModule->settings['login-enabled'] === false) return '';

		// Проверяем, вошел ли пользователь
		// ---------------------------------
		if (!empty(\Core::getModule('users')->user)) {
			return $this->renderLogout();
		}
		else {
			return $this->renderLogin();
		}
     

	}
}