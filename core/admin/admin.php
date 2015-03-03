<?php

namespace Core\Modules;

class Admin extends \Module {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'admin',
		'title' => 'Панель администрирования',
		'initOrder' => 1,
		'hasSettings' => true
	);

	// Module settings
	// ---------------
	public static $settings = array(
		'visibility' => 'developers'
	);

	// Module settings format
	// ----------------------
	public static $componentSettingsFormat = array(
		array('id' => 'enabled', 'type' => 'boolean', 'title' => 'Включено'),
		array('id' => 'theme', 'type' => 'select', 'title' => 'Тема оформления', 'value' => 'default'),
		array('id' => 'visibility', 'type' =>'select', 'title' => 'Кто имеет доступ', 'values' => array('all' => 'все', 'logged' => 'вошедшие', 'admins' => 'администраторы', 'developers' => 'разработчики'))
	);

	// Toggle edit mode
	// ----------------
	public static function actionToggleEditMode($args = null) {
		if (@ $_SESSION['editMode'] != true) $_SESSION['editMode'] = true;
		else $_SESSION['editMode'] = false;
		\Client::reload();
	}

	// Init component
	// --------------
	public static function initComponent() {

		// Auto add admin widget
		// ---------------------
		if (!empty(\Core::getModule('users')->user)) {
			\Events::send('addWidget', array('admin-toolbar', null, array('block' => 'auto')));
		}

	}

}
