<?php

namespace Robokassa\Packages;

class Robokassa extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'robokassa',
		'title' => 'Robokassa',
		'description' => 'Модуль позволяет производить on-line платежи через систему robokassa. Сайт сервиса http://robokassa.ru/ru/',
		'icon' => '/packages/robokassa/icon.jpg',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);
	
}	