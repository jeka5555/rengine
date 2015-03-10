<?php

namespace Core\Modules;

class Log extends \Core\Module
{

	// Basic properties
	// ----------------
	public $enabled = true;
	public $useDatabase = true;
	public $logsDirectory = __DR__ . 'private/logs';

	// Router configuration
	// --------------------
	public $router = array(
		'default' => array('fileName' => 'log', 'rotationFrequency' => 'daily'),
		'debug' => array('fileName' => 'debug', 'rotationFrequency' => 'daily'),
		'error' => array('fileName' => 'error'),
		'warning' => array('fileName' => 'warning'),
		'access' => array('fileName' => 'access')
	);

	// Log
	// ---
	public function log($text, $type = null) {

		// Get type data or return
		// -----------------------
		if (!empty($this->router['default'])) $typeData = $this->router['default'];
		else if (!empty($type) && !empty($this->router[$type])) $typeData = $this->router[$type];
		else return;

		// Get user ID
		// -----------
		$userID = null;
		if ($userModule = @\Core::getModule('users')) $userID = @ $userModule->user->_id;

		// Save to database
		// -----------------
		if (!empty($type) && ($type != 'debug') && @ $this->useDatabase == true) {
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
		$rows['user'] = array('ip' => $_SERVER['REMOTE_ADDR'], 'browser' => $_SERVER['HTTP_USER_AGENT']);

		// Collect user info
		// -----------------
		if (!empty($userID)) {
			$rows['user']['id'] = $userID;
			$rows['user']['fullName'] =  @ $userModule->user->fullName;
		}

		// Write to file
		// -------------
		if (@$typeData['enabled'] !== false && !empty($typeData['fileName'])) {

			$fileName = __DR__ . 'private/logs/' . $typeData['fileName'];

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
			$line = json_encode($rows);
			file_put_contents($fileName, $line, FILE_APPEND);
		}
	}

	// Install module
	// --------------
	public function installModule() {
		mkdir(__DR__ . '/private/log', 0777);
	}

}
