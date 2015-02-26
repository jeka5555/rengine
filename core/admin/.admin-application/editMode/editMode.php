<?php

namespace Core\Admin\AdminApplications;

// Класс приложения для медиа-менеджера
// ------------------------------------
class EditMode extends \Core\Admin\Components\AdminApplication{

	// Описание компонента
	// -------------------
	public static $component = array(
		'addOnToolbar' => true,
		'id' => 'editMode',
		'title' => 'Включение режима редактора',
		'icon' => '/core/admin/.admin-application/editMode/icon.png',
		'access' => array(
			array(
				'type' => 'or',
				'rules' => array(
					array('type' => 'userRole', 'role' => 'administrator'),
					array('type' => 'userRole', 'role' => 'super'),
					array('type' => 'userRole', 'role' => 'content-manager'),
					array('type' => 'userRole', 'role' => 'seo')
				)
			)
		)
	);


	// Action
	// ------
	public static function getExecuteCommand() {
		return 'API.action({ "action" : "/module/apps/editMode/toggle" });';
	}

	// Toggle mode
	// -----------
	public function commandToggle($data = null) {
		if (@ $_SESSION['editMode'] != true ) {
			$_SESSION['editMode'] = true;
		}
		else {
			$_SESSION['editMode'] = false;
		}


		\Events::send('reload');

	}

}