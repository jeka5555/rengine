<?php

// Request
// -------
class Request extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'request',
		'title' => 'Хранилище запроса'
	);

	// Variables
	// ---------
	public $isAJAX = false; // Is AJAX request
	public $headers = array(); // Headers data
	public $data = null; // Request data
	public $path = array(); // Path
	public $uri = null; // Request URI
	public $host = ''; // Host

	// Create new request
	// ------------------
	public static function getInstance($args = array()) {
		$instance = parent::getInstance($args);
		$instance->path = \URI::parseURI(@ $instance->uri);
		return $instance;
	}

	// Parse headers
	// -------------
	public static function parseHeaders() {

		// If function is embedded
		// -----------------------
		if (function_exists('getallheaders')) {
			$headers = getallheaders();
		}

		// Of just emulate it
		// -------------------
		$headers = array();

		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$name = str_replace(' ', '-', str_replace('_', ' ', substr($name, 5)));
				$headers[$name] = $value;
			}
			else if ($name == "CONTENT_TYPE") $headers["Content-Type"] = $value;
			else if ($name == "CONTENT_LENGTH") $headers["Content-Length"] = $value;
		}

		// Return headers
		// --------------
		return $headers;
	}

}