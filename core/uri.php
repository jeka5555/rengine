<?php

// Базовый класс для ссылок
// ------------------------------------
class URI extends \Module {


	public static $component = array(
		'id' => 'uri',
		'title' => 'Модуль управления ссылками'
	);

	// Request data
	// ------------
	public $in = array();
	public $out = array();

	// Add link data
	// --------------
	public function addURIData($data = null, $key = false, $replace = false) {

		// Return
		// ------
		if ($key === null) return;

		// For real key
		// ------------
		if ($key != false) {
			// Old data
			// --------
			$outData = first_var(@ $this->out[$key], array());

			if ($replace == true) $out[$key] = $data;
			else $this->out[$key] = array_merge($outData, $data);
		}

		// Or add wihtout key
		// ------------------
		else {
			$this->out = array_merge($this->out, $data);
		}
	}

	// Get data with URI
	// -----------------
	public function buildURI($options = array()) {

		// Get base
		// --------
		$baseURI = '';
		if (!empty($options['targetPage'])) $baseURI = $options['targetPage'];
		else if (@ $options['keepPage'] != false) $baseURI = $_SERVER['REQUEST_URI'];

		// Build complete URI
		// ------------------
		$result = $baseURI;

		// Data
		// ----
		if (@ $options['isolate'] == true) $data = @ $options['data'];
		else $data = $this->out;

		// Append object data
		// ------------------
		if (!empty($options['data'])) {
			$data = array_merge_recursive($data, $options['data']);
		}

		// Try to add query
		// ----------------
		$query = http_build_query($data);
		if (!empty($query)) $result .= '?'.$query;

		// Result
		// -----
		return $result;
	}

	// Разбор одного элемента
	// ----------------------
	public static function parseURI($uri) {

		$result = array();

		// Разбор запроса
		// --------------------------
		$url = preg_replace('/\?.*/i', '', $uri);
		$url = explode("/", $url);

		// Отделяем элементы
		// --------------------------
		if(preg_match("/\?.+/", $url[count($url)-1])) unset($url[count($url)-1]);

		// Удаляем пустые элементы
		// --------------------------
		foreach($url as $path) if(trim($path) != '') $result[] = $path;
		return $result;

	}

	// Parse headers
	// -------------
	public static function parseHeaders() {

		// If function is embedded
		// -----------------------
		if (function_exists('getallheaders')) {
			$headers = getallheaders();
			return $headers;
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

	// Parse GET
	// ---------
	public static function parseGET($line) {

		$url = parse_url($line);
		$get = array();

		if (!empty($url['query'])) parse_str($url['query'], $get);
		return $get;

	}

}
