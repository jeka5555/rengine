<?php

namespace Core\Modules;

class Log extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'log',
		'title' => 'Журнал событий',
		'hasSettings' => true
	);

	// Settings
	// --------
	public static $settings = array(
		'enabled' => true,
		'useDatabase' => true,
		'router' => array(
			'default' => array('fileName' => 'log', 'rotationFrequency' => 'daily'),
			'debug' => array('fileName' => 'debug', 'rotationFrequency' => 'daily'),
			'error' => array('fileName' => 'error'),
			'warning' => array('fileName' => 'warning'),
			'access' => array('fileName' => 'access')
		)
	);

	// Settings
	// --------
	public static $componentSettingsFormat = array(
		'enabled' => array('title' => 'Включить', 'type' => 'boolean'),
		'useDatabase' => array('type' => 'boolean', 'title' => 'Записывать в базу данных' ),
		'router' => array('title' => 'Настройка роутера логов', 'type' => 'record', 'format' => array(

			// All messages
			// ------------
			'default' => array('title' => 'По-умолчанию', 'type' => 'record', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Активно'),
				'fileName' => array('type' => 'text', 'title' => 'Имя файла'),
				'rotationFrequency' => array('type' => 'select', 'title' => 'Частота ротации', 'values' => array('daily' => 'Ежедневно', 'hourly' => 'Каждый час', 'weekly' => 'Раз в неделю', 'monthly' => 'Раз в год'))
			)),

			// Debug
			// -----
			'debug' => array('title' => 'Отладочная информация', 'type' => 'record', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Активно'),
				'fileName' => array('type' => 'text', 'title' => 'Имя файла'),
				'rotationFrequency' => array('type' => 'select', 'title' => 'Частота ротации', 'values' => array('daily' => 'Ежедневно', 'hourly' => 'Каждый час', 'weekly' => 'Раз в неделю', 'monthly' => 'Раз в год'))
			)),

			// Error
			// -----
			'error' => array('title' => 'Ошибки', 'type' => 'record', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Активно'),
				'fileName' => array('type' => 'text', 'title' => 'Имя файла'),
				'rotationFrequency' => array('type' => 'select', 'title' => 'Частота ротации', 'values' => array('daily' => 'Ежедневно', 'hourly' => 'Каждый час', 'weekly' => 'Раз в неделю', 'monthly' => 'Раз в год'))
			)),

			// Warnings
			// --------
			'warning' => array('title' => 'Предупреждения', 'type' => 'record', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Активно'),
				'fileName' => array('type' => 'text', 'title' => 'Имя файла'),
				'rotationFrequency' => array('type' => 'select', 'title' => 'Частота ротации', 'values' => array('daily' => 'Ежедневно', 'hourly' => 'Каждый час', 'weekly' => 'Раз в неделю', 'monthly' => 'Раз в год'))
			)),

			// Access
			// ------
			'access' => array('title' => 'Сообщения доступа', 'type' => 'record', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Активно'),
				'fileName' => array('type' => 'text', 'title' => 'Имя файла'),
				'rotationFrequency' => array('type' => 'select', 'title' => 'Частота ротации', 'values' => array('daily' => 'Ежедневно', 'hourly' => 'Каждый час', 'weekly' => 'Раз в неделю', 'monthly' => 'Раз в год'))
			)),

		))
	);

	// Write log
	// ---------
	public static function writeLog($text, $type = null) {
		$log = \Core::getModule('log');
		if(!empty($log)) {
			$log->log($text, $type);
		}
	}

	// Функция лога
	// ------------
	public function log($text, $type = null) {

		// Get type info
		// -------------
		$typeData = array();
		if (!empty(static::$settings['router']['default'])) $typeData = static::$settings['router']['default'];
		if (!empty($type) && !empty(static::$settings['router'][$type])) $typeData =  static::$settings['router'][$type];

		// Skip empty
		// ----------
		if (empty($typeData)) return;

		// Get user ID
		// -----------
		$userID = null;
		if ($userModule = @\Core::getModule('users')) {
			$userID = @ $userModule->user->_id;
		}

		// Save to database
		// -----------------
		if (!empty($type) && ($type != 'debug') && @ static::$settings['useDatabase'] == true ) {
			$messageClass = \Core::getClass('log-message');

			$message = $messageClass::getInstance(array( 'user' => $userID, 'message' => $text, 'time' => time(), 'type' => $type	));
			$message->save();
		}

		// Row of data
		// -----------
		$rows = array();

		// Add time
		// --------
		$rows['time'] = date("Y-m-d H:i:s");
		$rows['type'] = $type;
		$rows['message'] = $text;

		// Add user IP
		// ----------
		$rows['user'] = array(
			'ip' => $_SERVER['REMOTE_ADDR'],
			'browser' => $_SERVER['HTTP_USER_AGENT']
		);

		// Collect user info
		// -----------------
		if (!empty($userID)) {
			$rows['user']['id'] = $userID;
			$rows['user']['fullName'] =  @ $userModule->user->fullName;
		}

		// Write to file
		// -------------
		if (@$typeData['enabled'] !== false && !empty($typeData['fileName'])) {

			$fileName = __DR__.'logs/'.$typeData['fileName'];

			// File name by rotation frequency
			// -------------------
			if (!empty($typeData['rotationFrequency'])) {
				switch ($typeData['rotationFrequency']) {
					case 'daily': $fileName .= '.'.date("Y-m-d"); break;
					case 'monthly': $fileName .= '.'.date("Y-m"); break;
					case 'hourly': $fileName .= '.'.date("Y-m-d-H"); break;
				}
			}

			$fileName .= ".log";

			// Format and write
			// ----------------
			$line = yaml_emit($rows, YAML_UTF8_ENCODING, YAML_ANY_BREAK);
			file_put_contents($fileName, $line, FILE_APPEND);
		}
	}

	// Install module
	// --------------
	public function installModule() {
		mkdir(__DR__.'/log', 0777);
	}

}
