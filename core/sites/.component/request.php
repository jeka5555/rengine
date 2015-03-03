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
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			return $headers;
		}

	}

}