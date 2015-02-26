<?php

namespace Modules;

class API extends \Module {

	// Component
	// ---------
	public static $component = array('id' => 'api', 'title' => 'API-интерфейс');

	// Process packets
	// ---------------
	public function actionPacketRequest($data = array()) {

		// Here we keep packet results
		// ---------------------------
		$result = array();

		// If we have data
		// ---------------
		if (!empty($data)) {

			// Process each request
			// --------------------
			foreach ($data as $requestIndex => $request) {
				$result[] = $this->actionRequest(@ $request['action'], @ $request['data']);
			}
		}

		// Return result
		// -------------
		return $result;

	}

	// Process single request
	// ----------------------
	public function actionRequest($action = null, $data = array()) {

		// Create new request
		// ------------------
		$requestClass = \Core::getComponent('component', 'request');
		$request = $requestClass::getInstance(array(
			'uri' => $action,
			'data' => $data
		)); 

		// Process
		// -------
		$result = \Core::getApplication()->route($request);
		return $result;

	}

}
