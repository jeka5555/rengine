<?php

namespace Packages;

class SMSaero extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'smsaero',
		'title' => 'SMS Aero. Быстрые SMS-рассылки',
		'description' => 'Модуль позволяет отправлять SMS сообщения через сервис smsaero.ru',
		'icon' => '/packages/smsaero/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);
	
}	