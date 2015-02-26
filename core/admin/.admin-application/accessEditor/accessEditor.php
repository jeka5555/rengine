<?php

namespace Apps;

class AccessEditor extends \Core\Admin\Components\AdminApplication {

	// Информация о приложении
	// -----------------------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'accessEditor',
		'title' => 'Управление доступом'
	);

	// Инициализация
	// -------------
	function commandInit($args = array()) {

			// Ничего
			// ------
			if (empty($args['class']) || empty($args['id'])) return null;

			// Возврат данных на доступ
			// ------------------------
			return array(
				'actions' => @ $actions,
				'access' => @ $access,
				'owner' => @ $owner
			);

	}

}