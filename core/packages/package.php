<?php

namespace Repository\Packages;

class Repository extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'repository',
		'title' => 'Диспетчер пакетов',
		'description' => 'Модуль позволяет управлять скаченными пакетами и устанавливать новые из репозитория',
		'icon' => '/packages/repository/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);

}