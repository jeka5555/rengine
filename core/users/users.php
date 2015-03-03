<?php

namespace Core\Modules;

class Users extends \Module
{

	public $user = null;
	public $userID = null;

	// Component
	// ---------
	static public $component = array(
		'id' => 'users',
		'title' => 'Пользователи',

		// Settings
		// --------
		'hasSettings' => true,
	);

	// Component settings format
	// -------------------------
	public static $componentSettingsFormat = array(

		// General settings
		// ----------------
		'allowLogin' => array('type' => 'boolean', 'title' => 'Разрешить вход'),
		'allowRegistration' => array('type' => 'boolean', 'title' => 'Разрешить регистрацию'),

		// Login
		// -----
		'loginFormat' => array('type' => 'select', 'title' => 'Формат логина', 'values' => array('text' => 'Текст', 'email' => 'Электронная почта', 'phone' => 'Номер телефона')),
		'requireRegisterConfirmation' => array('type' => 'boolean', 'title' => 'Требовать подтверждение регистрации'),
		'passwordTemplate' => array('type' => 'text', 'title' => 'Допустимый формат пароля'),
		'registrationConfirmationTimeout' => array('type' => 'number', 'title' => 'Максимальное время ожидания подтверждения'),

		// Social
		// ------
		'allowSocialRegistration' => array('type' => 'boolean', 'title' => 'Разрешить регистрацию/вход из социальных сетей'),
		'allowedSocialNetworks' => array('type' => 'list', 'title' => 'Какие социальные сети использовать для регистрации?')
	);

	// Settings
	// --------
	public static $settings = array(

		// Login
		// -----
		'allowLogin' => true,
		'loginFormat' => 'email-or-phone',

		'allowRegistration' => true,
		'passwordTemplate' => null,

		// Register confirmation features
		// ------------------------------
		'registerConfirmationEnabled' => true,
		'registerConfirmationType' => 'email',


	);


	// Get structure of editor
	// -----------------------
	public function getComponentEditorStructure()
	{

		return array(

			// General parameters
			// ------------------
			array('type' => 'block', 'title' => 'Общие параметры',
				'elements' => array(
					array('type' => 'form', 'properties' => array('allowLogin', 'allowRegistration'))
				)
			),

			// Authorization
			// -------------
			array('type' => 'block', 'title' => 'Параметры авторизации',
				'elements' => array(
					array('type' => 'form', 'properties' => array('loginFormat', 'passwordTemplate', 'requireRegisterConfirmation', 'registrationConfirmationTimeout'))
				)
			),

			// Social authorization
			// --------------------
			array('type' => 'block', 'title' => 'Социальная авторизация',
				'elements' => array(
					array('type' => 'form', 'properties' => array('allowSocialRegistration', 'allowedSocialNetworks'))
				)
			)
		,
		);

	}

	// Проверка на роль
	// ----------------------
	public function hasRole($role)
	{
		if (empty($this->user->roles)) return false;
		if (in_array($role, @ $this->user->roles)) return true;
		return false;
	}

	// Проверка на наличие в системе
	// ----------------------
	public function isLogged()
	{
		return (!empty($this->user));
	}

	// Проверка на наличие права
	// ----------------------
	public function hasPermission($permission)
	{
		if (empty($this->user->permissions)) return false;
		if (in_array($permission, $this->user->permissions)) return true;
		return false;
	}

	// Логин
	// -----------------------------
	public function logout($args = array())
	{

		// Log
		// ---
		\Logs::log(array(
			'action' => 'logout',
			'message' => 'Выход пользователя'
		), 'access');

		// Remove session data
		// -------------------
		unset($_SESSION['sessionID']);
		unset($_SESSION['isSuperUser']);

		// Save user session ID
		// --------------------
		if (!empty($this->user)) {
			$this->user->set('sessionID', null);
			$this->user->save();
		}

		// Event
		// -----
		\Events::send('logout');
		\Events::send('setLocation', '/');

	}

	// Init module
	// -----------
	public function init()
	{

		// Get user class
		// --------------
		$userClass = \Core::getComponent('class', 'user');

		// Superuser
		// ---------
		if (@ $_SESSION['isSuperUser'] == true) {

			$this->user = new $userClass(array(
				'nick' => 'super',
				'firstName' => 'Суперпользователь',
				'roles' => array('administrator', 'super'),
				'@deleted' => true
			));

			return true;
		}

		// Если пользователь не залогинен, то попробуем залогинется из cookies
		// -----------------------
		if (!isset($_SESSION['sessionID']) and isset($_COOKIE['sessionID']))
			$_SESSION['sessionID'] = $_COOKIE['sessionID'];

		if (!isset($_SESSION['currentRole']) and isset($_COOKIE['currentRole']))
			$_SESSION['currentRole'] = $_COOKIE['currentRole'];

		// If no any session, exit
		// -----------------------
		if (!isset($_SESSION['sessionID'])) return false;

		// Read session user
		// -----------------
		$user = $userClass::findOne(array('query' => array('sessionID' => $_SESSION['sessionID'], 'isActive' => true)));

		// Convert roles to ID's
		// ----------------------
		if (!empty($user)) {
			$user->roles = array();
			$user->permissions = array();
		}

		if (!empty($user->userRoles)) {

			// Get role class
			// --------------
			$rolesClass = \Core::getClass('userRole');
			$userPermissionClass = \Core::getClass('userPermission');

			// Read roles id
			// -------------
			foreach ($user->userRoles as $role) {

				$roleData = $rolesClass::findPK($role);
				if (empty($roleData)) continue;

				// Set id
				// ------
				$user->roles[] = first_var(@ $roleData->id, $roleData->_id);

				if (!empty($roleData->permissions)) {
					$permissions = $userPermissionClass::find(array('query' => array('_id' => array('$in' => $roleData->permissions))));
					if (!empty($permissions)) {
						foreach ($permissions as $permission) {
							$user->permissions[] = first_var(@ $permission->id, $permission->_id);
						}
					}
				}

			}
		}

		if (!empty($user->userPermissions)) {
			$userPermissionClass = \Core::getClass('userPermission');
			foreach ($user->userPermissions as $permission) {
				$permission = $userPermissionClass::findPK($permission);
				$user->permissions[] = first_var(@ $permission->id, $permission->_id);
			}
		}

		// Update module data
		// -----------------------
		if (!empty($user)) {

			// Set user
			// --------
			$this->user = $user;
			$this->userID = @$user->_id;

			// Update event time
			// -----------------
			if (!empty($user->lastEventTime)) {
				$_SESSION['lastEventTime'] = $user->lastEventTime;
			}
		}

	}

	// Login
	// -----
	public function login($args = array())
	{

		// Nothing
		// -------
		if (empty($args['email'])) return;

		// Superuser
		// ---------
		if ($args['email'] == @ \Core::$settings['siteID'] && $args['password'] == \Core::$settings['siteKey']) {

			// Log
			// ---
			\Logs::log(array(
				'action' => 'login',
				'developer' => true,
				'message' => 'Вход суперпользователя',
			), 'access');

			// Save user status in session
			// ---------------------------
			$_SESSION['isSuperUser'] = true;

			// Reload page
			// -----------
			\Events::send('reload');
			return true;
		}

		// Logout user
		// -----------
		if (!empty($this->user)) $this->logout();

		// Get user class
		// --------------
		$userClass = \Core::getClass('user');

		// Read user
		// ---------
		$user = $userClass::findOne(array('query' => array('primaryEmail' => $args['email'], 'password' => md5(@ $args['password']), 'isActive' => true)));


		// If no user, log error
		// ---------------------
		if (empty($user)) {

			// Ставим сессию
			// --------------
			\Logs::log(array('action' => 'loginFail', 'message' => 'Неверное имя пользователя или пароль', 'password' => $args['password'], 'email' => $args['email'],), 'access');

			// Message for user
			// ----------------
			\Events::send('message', array('text' => 'Пользователь не существует', 'type' => 'error'));
			return false;
		}

		// Уникальный идентификатор сессии
		// ---------------------
		$sessionID = md5(uniqid());
		$_SESSION['sessionID'] = $sessionID;
		$user->set('sessionID', $sessionID);
		$user->save();

		setcookie("sessionID", $sessionID, time() + 315360000, "/");

		// Протоколируем
		// -------------
		$this->user = $user;

		// Ставим сессию
		// --------------
		\Logs::log(array('action' => 'loginSuccess', 'message' => 'Вход пользователя выполнен', 'sessionID' => @ $sessionID,), 'access');

		// Перезагрузка
		// ------------
		\Events::send('login');

		// Результат
		// ---------
		return true;

	}

	// User is inside group
	// --------------------
	public function isInsideGroup($groupID)
	{

		// Get group class
		// ---------------
		$groupClass = \Core::getClass('userGroup');
		if (!empty($groupClass)) {

			$group = $groupClass::findPK($groupID);

			if (!empty($group)) {
				if (is_array($group->users) && in_array($groupID, $group->users)) return true;
			}
		}

		return false;
	}

	// Login
	// -----
	public static function actionLogin($args = array())
	{
		@ \Core::getModule('users')->login(array('email' => @ $args['email'], 'password' => @ $args['password']));
	}

	// Logout
	// ------
	public static function actionLogout()
	{
		@ \Core::getModule('users')->logout();
	}

	// Request login form
	// ------------------
	public function actionRequestLoginForm()
	{
		\Popups::show('user-login');
	}


}
