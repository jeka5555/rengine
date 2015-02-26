<?php

class Email extends Module {

	// Инициализация компонента
	// ------------------------
	static $component = array(
		'id' => 'email',
		'title' => 'Отправка почтовых сообщений',

		// Module settings
		// ---------------
		'hasSettings' => true,
	);


	// Settings format
	// ---------------
	public static $componentSettingsFormat = array(
		'server' => array('type' => 'text', 'title' => 'Адрес почтового сервера'),
		'port' => array('type' => 'number', 'title' => 'Номер порта сервера', 'value' => 25),
		'useCrypto' => array('type' => 'boolean', 'title' => 'Использовать защиту', 'value' => false ),
		'user' => array('type' => 'text', 'title' => 'Имя пользователя'),
		'pass' => array('type' => 'text', 'title' => 'Пароль пользователя', 'isPassword' => true)
	);

	// Settings
	// --------
	public static $settings = array(
		'server' => 'smtp.yandex.ru',
		'port' => 25,
		'crypto' => false,
		'user' => '',
		'pass' => ''
	);

	// Настройки модуля
	// ----------------
	public $server;
	public $port;
	public $crypto;
	public $user;
	public $pass;
	public $log;

	public $timeout = '45';
	public $localhost = 'localhost';
	public $nl = "\r\n";
	public $conn;

	/**
	 * Соединяемся с сервером
	 */
	private function connect() {

		$this->crypto = strtolower(trim($this->crypto));
		$this->server = strtolower(trim($this->server));

		if($this->crypto == 'ssl')
			$this->server = 'ssl://' . $this->server;
		$this->conn = fsockopen(
			$this->server, $this->port, $errno, $errstr, $this->timeout
		);
		$this->log .= fgets($this->conn);
		return;
	}

	/**
	 * Авторизация.
	 */
	private function  auth() {
		fputs($this->conn, 'HELO ' . $this->localhost . $this->nl);
		$this->log .= fgets($this->conn);
		if($this->crypto == 'tls') {
			fputs($this->conn, 'STARTTLS' . $this->nl);
			$this->log .= fgets($this->conn);
			stream_socket_enable_crypto(
				$this->conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT
			);
			fputs($this->conn, 'HELO ' . $this->localhost . $this->nl);
			$this->log .= fgets($this->conn);
		}
		if($this->server != 'localhost') {
			fputs($this->conn, 'AUTH LOGIN' . $this->nl);
			$this->log .= fgets($this->conn);
			fputs($this->conn, base64_encode($this->user) . $this->nl);
			$this->log .= fgets($this->conn);
			fputs($this->conn, base64_encode($this->pass) . $this->nl);
			$this->log .= fgets($this->conn);
		}
		return;
	}

		// Отправка письма
		// ----------------------------------------------
		public function send($args=array()) {

			$this->server = first_var(@ $args['server'], @ static::$settings['server']);
			$this->port = first_var(@ $args['port'], @ static::$settings['port']);
			$this->crypto = first_var(@ $args['crypto'], @ static::$settings['crypto']);
			$this->user = first_var(@ $args['user'], @ static::$settings['user']);
			$this->pass = first_var(@ $args['pass'], @ static::$settings['pass']);

			$this->log = "";
			Email::connect();
			Email::auth();

			// Email получателя
			if(!empty($args['to'])) $to = $args['to']; else return false;

			// Email отправителя
			if(!empty($args['from'])) $from = $args['from']; else return false;

			// Название/Имя отправителя
			if(!empty($args['fromName'])) $fromName = $args['fromName']; else $fromName = stripslashes($_SERVER['HTTP_HOST']);

			// Тема письма
			if(!empty($args['task'])) $task = $args['task']; else $task = '';

			// Текст письма
			if(!empty($args['message'])) $message = '<!DOCTYPE html><html><head><title>'.$fromName.'</title></head><body>'.$args['message'].'</body></html>'; else return false;

			// Вложение
			if(!empty($args['attachment'])) $attachment = $args['attachment'];

			// Создаем тему
			// ----------------------------------------------
			$subject = $task;
			$subject = '=?utf-8?B?'.base64_encode($subject).'?=';

			// Создаем заголовки
			// ----------------------------------------------
			$headers = 'Content-type: text/html; charset=utf-8';

			$snd = 'MAIL FROM: '. $from .''. $this->nl;
			$this->log .= '>>> ' . $snd;
			fputs($this->conn, $snd);
			$this->log .= '<<< ' . fgets($this->conn);
			$snd = 'RCPT TO: <'. $to .'>'. $this->nl;
			$this->log .= '>>> ' . $snd;
			fputs($this->conn, $snd);
			$this->log .= '<<< ' . fgets($this->conn);
			$snd = 'DATA'. $this->nl;
			$this->log .= '>>> ' . $snd;
			fputs($this->conn, $snd);
			$this->log .= '<<< ' . fgets($this->conn);
			fputs($this->conn,
				'From: '.$from .$this->nl.
				'To: '. $to .$this->nl.
				'Subject: '. $subject .$this->nl.
				$headers .$this->nl.
				$this->nl.
				$message . $this->nl.
				'.' .$this->nl
			);
			$this->log .= '<<< ' . $answer = htmlspecialchars(fgets($this->conn));
			//var_dump($this->log);
			return (preg_match("/^250[^\d]+.*/", $answer)?1:false);
		}


	// Выход и отсоединение.
	// ----------------------------------------------
	function destruct() {
		fputs($this->conn, 'QUIT' . $this->nl);
		fgets($this->conn);
		fclose($this->conn);
	}
}
