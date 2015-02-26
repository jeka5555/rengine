<?php

namespace Core\Users;

class UserProfile extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'user-profile',
		'title' => 'Страница пользовательского профиля'
	);

	// Node data format
	// ----------------
	public static function getNodeDataFormat() {
		return array(
			'useDefaultUser' => array('type' => 'boolean', 'title' => 'Использовать текущего пользователя по-умолчанию'),
			'otherProfilesAccess' => array('type' => 'select', 'title' => 'Отображение чужих профилей', 'values' => array('all' => 'Все', 'onlyActive' => 'Только активные', 'no' => 'Не показывать чужие профили'))
		);
	}

	// Process path
	// ------------
	public function processPath($path, $index = 0) {
	
		parent::processPath($path, $index);
	
		// Set title
		// ---------
		if (!empty($this->title)) {
			\Core::getModule('sites')->setTitle($this->title);
		}

		// What user is visible
		// --------------------
		$pageID = 'home';
		$userProfileID = @ \Core::getModule('users')->user->_id;

		// What page we want to see
		// ------------------------
		$pagePath = @ $path[$index + 1];
		if (!empty($pagePath)) {

		}

		// Show page from list
		// -------------------
		if (empty($pageID)) {
			if (\Rules::checkRule(array('type' => 'userLogged'))) {
				$pageID = 'home';
			}
		}

		// Are we see other user's profile?
		// --------------------------------
		else {

		}

		// Find component
		// --------------
		$userPageComponent = @ \Core::getComponent('user-page', $pageID);
		if (!empty($userPageComponent)) {
		}

	}

}
