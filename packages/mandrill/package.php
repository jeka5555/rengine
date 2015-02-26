<?php

namespace Mandrill\Packages;

class Mandrill extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'mandrill',
		'title' => 'Mandrill',
		'description' => 'Модуль позволяет отправлять email сообщения через сервис mandrill. Сайт сервиса http://mandrill.com/',
		'icon' => '/packages/mandrill/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);
	
}	