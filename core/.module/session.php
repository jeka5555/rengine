<?php

namespace Core\Modules;

class Session extends \Module {

	// Component
	// ---------
	public static $component = array('id' => 'session', 'title' => 'Сессия пользователя');

	// Set session data
	// ----------------
	public function actionSet($args = array()) {

		// We need a real object
		// ---------------------
		if (empty($args['id'])) return false;
		$objectID = $args['id'];

		// We need to have any data
		// -------------------------
		if (!empty($args['data'])) $state = $args['data'];
		else $state = array();

		// Require session data
		// -------------------
		if (empty($_SEESION[$objectID])) $_SESSION[$objectID] = array();

		// Modify
		// ------
		switch (@ $args['mode']) {

			// Update
			// ------
			case "update":
				$_SESSION[$objectID] = array_merge($_SESSION[$objectID], $state);
				break;

			// Replace
			// -------
			default:
				$_SESSION[$objectID] = $state;
				break;
		}

	}

	// Clear object session
	// --------------------
	public function actionClear($args = array()) {
		unset($_SESSION[$args['id']]);
	}

}