<?php

namespace Modules;

class SMSaero extends \Module {

	// Инициализация компонента
	// ------------------------
	static $component = array(
		'id' => 'smsaero',
		'title' => 'Отправка SMS сообщений через сервис smsaero.ru',

		// Module settings
		// ---------------
		'hasSettings' => true,
	);


	// Settings format
	// ---------------
	public static $componentSettingsFormat = array(
		'server' => array('type' => 'text', 'title' => 'Путь до сервера API'),
		'user' => array('type' => 'text', 'title' => 'Имя пользователя'),
		'pass' => array('type' => 'text', 'title' => 'Пароль пользователя'),
		'from' => array('type' => 'text', 'title' => 'Подпись отправителя'),
	);

	// Settings
	// --------
	public static $settings = array(
		'server' => 'http://gate.smsaero.ru/send/',
		'user' => '',
		'pass' => '',
		'from' => '',
	);


	// Отправка письма
	// ----------------------------------------------
	public function send($args=array()) {
	
		$this->server = first_var(@ $args['server'], @ static::$settings['server']);
		$this->user = first_var(@ $args['user'], @ static::$settings['user']);
		$this->pass = md5(first_var(@ $args['pass'], @ static::$settings['pass']));
		$this->from = first_var(@ $args['from'], @ static::$settings['from']);
	
		// Телефон получателя
		if(!empty($args['to'])) $to = $args['to']; else return false;
	
		// Текст письма
		if(!empty($args['message'])) $message = $args['message']; else return false;
		
		
		$requestData = array(
			'to' => $to,
			'text' => $message,
			'user' => $this->user,
			'password' => $this->pass,
			'from' => $this->from 
		);	

		$result = file_get_contents($this->server.'?'.http_build_query($requestData));
		
		if (strpos($result, 'accepted') === false)
			return false;
		else
			return true;	 
		
	}
		
}
