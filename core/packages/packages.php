<?php

namespace Packages\Module;

class Packages extends \Module {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'packages',
		'title' => 'Пакеты расширений',
		'description' => 'Модуль позволяет управлять скаченными пакетами и устанавливать новые из репозитория',
		'hasSettings' => true
	);


	public static $componentSettingsFormat = array(
		'packagesDirectory' => array('type' => 'text', 'title' => 'Директория для хранения расширений', 'value' => '/packages'),
		'mainRepositoryURI' => array('type' => 'text', 'title' => 'Ссылка на основной репозиторий', 'value' => 'http://repository.rengine.ru')
	);

}