<?php

namespace Users\Classes;

class User extends \ObjectClass {

	// Component
	// ----------------------
	public static $component = array(
		'id' => 'user',
		'title' => 'Пользователь',
		'editable' => true
	);

	// Class indexes
	// -------------
	public static $classIndexes = array(
		array(array('primaryEmail' => 1)),
		array(array('password' => 1))
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(
			array('type' => 'or', 'rules' => array(
				array('type' => 'userRole', 'role' => 'super'),
				array('type' => 'userRole', 'role' => 'administrator'),
				array('type' => 'userRole', 'role' => 'content-manager')
			))
		),
		'read' => true
	);

	// Class actions
	// -------------
	public static $classActions = array(
		'block' => array('title' => 'Заблокировать', 'bulkAction' => true),
		'unblock' => array('title' => 'Разблокировать', 'bulkAction' => true)
	);

	// Class properties
	// ----------------
	public static $classProperties = array(

		// Name
		// ----
		'nick' => array('type' => 'text', 'title' => 'Имя пользователя', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'lastName' => array('type' => 'text', 'title' => 'Фамилия', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'firstName' => array('type' => 'text', 'title' => 'Имя', 'listing' => true, 'sortable' => true), 'searchable' => true,
		'middleName' => array( 'type' => 'text', 'title' => 'Отчество'),	

		// Login
		// ------
		'password' => array('type' => 'text', 'title' => 'Пароль', 'isPassword' => true),
		'primaryEmail' => array('type' => 'text', 'title' => 'Основной email', 'hint' => 'Данный емейл используется как логин дла входа', 'validator' => array('email', 'required'), 'listing' => true, 'searchable' => true),
		'confirmationKey' => array('title' => 'Ключ подтверждения регистрации', 'type' => 'text'),
		'recoverKey' => array('title' => 'Ключ восстановления', 'type' => 'text'),

		// Passport
		// --------
		'gender' => array('type' => 'select', 'title' => 'Пол', 'values' => array('m' => 'мужской', 'f' => 'женский'),'allowEmpty' => true, 'searchable' => true ),
		'avatar' => array( 'type' => 'media', 'mediaType' => 'image', 'title' => 'Фото', 'folderPath' => array('Пользователи')),

		// Contacts
		// --------
		//'phone' => array('type' => 'text', 'title' => 'Номер телефона'),

		// Additional
		// ----------
		'isActive' => array('type' => 'boolean', 'title' => 'Включен', 'value' => true, 'listing' => true, 'searchable' => true),
		//'balance' => array('type' => 'number', 'precision' => 'float', 'title' => 'Баланс пользователя', 'access' => array(array('type' => 'userRole', 'role' => 'administrator'))),

		// Managed cities
		// --------------
		'cities'  => array('type' => 'list', 'title' => 'Города для управления', 'format' => array('type' => 'object', 'class' => 'geo-city')),

		// Access
		// ------
		'userRoles' => array('type' => 'list', 'title' => 'Роли пользователя', 'listing' => true, 'searchable' => true,
			'access' => array(array('type' => 'userRole', 'role' => 'administrator')),
			'format' => array('type' => 'object', 'class' => 'userRole')
		),

		'userPermissions' => array('type' => 'list', 'title' => 'Права пользователя',
			'access' => array(array('type' => 'userRole', 'role' => 'administrator')),
			'format' => array('type' => 'object', 'class' => 'userPermission')
		),

	);


	// Get editor structure
	// --------------------
	public function getEditorStructure() {

		$structure = parent::getEditorStructure();

		// Passport data
		// -------------
		$structure[0]['elements']['main']['elements']['loginBlock'] = array(
			'type' => 'block', 'title' => 'Данные регистрации',
			'elements' => array(
				array(
					'type' => 'form',
					'properties' => array('password', 'primaryEmail', 'recoverKey', 'confirmationKey')
				)
			)
		);


		// Passport data
		// -------------
		$structure[0]['elements']['main']['elements']['passportBlock'] = array(
			'type' => 'block', 'title' => 'Паспортные данные',
			'elements' => array(
				array(
					'type' => 'form',
					'properties' => array('gender', 'avatar')
				)
			)
		);

		// Roles and proveleges
		// --------------------
		$structure[0]['elements']['main']['elements']['resourceBlock'] = array(
			'type' => 'block', 'title' => 'Права и привелегии',
			'elements' => array(
				array(
					'type' => 'form',
					'properties' => array('cities', 'userRoles', 'userPermissions')
				)
			)
		);

		return $structure;
	}
	
	public function save($args = array()) {
		$this->roles = null;
		parent::save();
	}

	// Change avatar
	// -------------
	public function actionChangeAvatar($avatar = null) {

		// Require avatar ID
		// -----------------
		if(empty($avatar)) {
			\FlashMessages::add(array('type' => 'error', 'text' => 'Не передан ID Media для изменения аватарки'));
			return false;
		}

		// Set avatar
		// ----------
		$this->avatar = $avatar;
		$this->save();

		// Reload user's page
		// ------------------
		\Events::send('reload');

	}

	// Link to user's profile
	// ----------------------
	public function getProfileURI() {
		return '/profile/'.$this->_id;
	}

	// Render avatar image
	// -------------------
	public function renderAvatar($args = array()) {

		$content = '';
		$avatarURI = @static::$component['packagePath'].'/.assets/img/user.png';


		// Get from user's avatar
		// ----------------------
		if (!empty($this->avatar)) {
			$mediaClass = \Core::getComponent('class', 'media');
			$avatar = $mediaClass::findPK($this->avatar);
			if (!empty($avatar)) $avatarURI = $avatar->getURI();
		}

		// Prepare avatar
		// --------------
		if (!empty($avatarURI)) {
			$image = new \Image(array('sourceFile' => __DR__.$avatarURI, 'effects' => array(
				array(
					'type' => 'resize',
					'width' => first_var(@ $args['width'], 80),
					'height' => first_var(@ $args['height'], 80),
					'scaleMode' => first_var(@ $args['mode'], 'cover') 
				)
			)));

            $content = '<div class="user-avatar"><a href="/profile/'.$this->_id.'"><img class="has-border" src="'.$image->getURI().'" /></a></div>';
		}

		// Return
		// ------
		return $content;

	}



	// Full name for user
	// -------------------
	public function getFullName() {

		// Merge name
		// ----------
		if (!empty($this->firstName) || !empty($this->lastName)) {
			return trim(@$this->lastName.' '.@$this->firstName.' '.@$this->middleName);
		}
		
		// Nickname
		// --------
		else if (!empty($this->nick)) {
			return $this->nick;
		}

		// Or use email
		// ------------
		return $this->primaryEmail;

	}

	// Получение идентификатора
	// ------------------------
	public function getIdentityTitle() {
		return $this->getFullName();
	}

	// Set new password
	// ----------------
	public function setPassword($password) {
		if ($password == @ $this->properties['password']) return;
		$this->properties['password'] = md5($password);
	}


	// Block user
	// ----------
	public function actionBlock() {
		$this->isActive = false;
		$this->save();
	}

	// Unblock user
	// ----------
	public function actionUnblock() {
		$this->isActive = true;
		$this->save();
	}


	// Проверка наличия роли
	// ---------------------
	public function hasRole($role) {
		return in_array($role, $this->roles);
	}

	// Проверка наличия правила
	// ------------------------
	public function hasPermission($permission) {
		return @in_array($permission, $this->permissions);
	}

	// Add role
	// --------
	public function addRole($role) {
		if (!in_array($role, $this->userRoles)) $this->userRoles[] = $role;
		$this->save();
	}

	// Add permission
	// --------------
	public function addPermission($permission) {
		if (!in_array($permission, $this->userPermissions)) $this->userPermissions[] = $permission;
		$this->save();
	}

	// Remove role
	// -----------
	public function removeRole($role) {
		if ($index = array_search($role, $this->userRoles)) unset($this->userRoles[$index]);
		$this->save();
	}

	// Remove permission
	// -----------------
	public function removePermision($permission) {
		if ($index = array_search($perimssion, $this->userPermissions)) unset($this->userPermissions[$index]);
		$this->save();
	}

}
