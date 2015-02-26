<?php

class UserPage extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'user-page',
		'autoload' => true,
		'title' => 'Пользователи.Страница профиля'
	);

	// Component settings format
	// -------------------------
	public static $componentSettingsFormat = array(
		'enabled' => array('type' => 'boolean', 'title' => 'Включено', 'value' => true),
		'title' => array('type' => 'text', 'title' => 'Название страницы'),
		'visibility' => array('type' => 'select', 'title' => 'Видимость', 'values' => array('user' => 'Только пользователь', 'friends' => 'Друзья', 'everybody' => 'Все')),
		'visibilityAccess' => array('type' => 'rules', 'title' => 'Правила доступа')
	);

}