<?php

namespace Core\Admin\AdminApplications;

// Класс приложения для медиа-менеджера
// ------------------------------------
class WidgetsPanel extends \Core\Admin\Components\AdminApplication{

	// Описание компонента
	// Инициализация компонента
	// ------------------------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'widgetsPanel',
		'title' => 'Панель виджетов',
		'icon' => '/core/admin/.admin-application/widgetsManager/icon.png',
		'access' => array(
			array('type' => 'userRole', 'role' => 'administrator')
		)
	);


	// Инициалиазция панели
	// --------------------
	public function commandInit($args = null) {

		// Информация по типам виджетов
		// ----------------------------
		$widgetTypes = array(
			'default' => array('id' => 'default', 'title' => 'Неизвестно'),
			'generic' => array('id' => 'generic', 'title' => 'Общие'),
			'resource' => array('id' => 'resource', 'title' => 'Вывод ресурсов'),
			'ran' => array('id' => 'ran', 'title' => 'Проект РАН'),
			'forum' => array('id' => 'forum', 'title' => 'Форум'),
			'ads' => array('id' => 'ads', 'title' => 'Реклама')
		);

		// Если виджеты есть
		// -------------------
		$widgetsList = array();
		if ($widgets = @ \Extension::$ext['widget']) {

			// Отбираем с флагом editable == true
			// ----------------------------------	
			foreach($widgets as $widget) {
				if (@ $widget['editable'] == true) {
					$widgetsList[] = array(
						'id' => $widget['id'],
						'title' => first_var(@ $widget['title'], @ $widget['id']),
						'color' => @ $widget['color'],
						'group' => @ $widget['group']
					);
				}
			}
		}


		// Возврат
		// -------
		return array('widgets' => $widgetsList, 'types' => $widgetTypes);

	}
}
