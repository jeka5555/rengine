<?php

// Класс для работы с сообщениями
// --------------------------------------
class Logs {

	public static $text = array();
	public static $types = array();

	// Функция лога
	// ------------
	public static function log($text, $type = 'log') {		


		$logModule = \Core::getModule('log');
		if (!empty($logModule)) {
			$logModule->writeLog($text, $type);
		}
	}

}