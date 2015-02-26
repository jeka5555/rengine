<?php

class Events extends \Module {

	public static $component = array('id' => 'events', 'title' => 'События');

	// Events storage
	// --------------
	public static $events = array();

	// Events listeners
	// -----------------
	public static $listeners = array(
		'*' => array()
	);

	// Add listener
	// ------------
	public static function addListener($event, $listener) {

		// All events
		// ----------
		if (is_null($event)) $event = '*';

		// If simple event
		// ---------------
		if (!is_array($event)) $event = array($event);

		// If list of events
		// -----------------
		foreach($event as $subevent) {

			// Create event
			// ------------
			if (!isset(static::$listeners[$subevent]) || !is_array(static::$listeners[$subevent])) {
				static::$listeners[$subevent] = array();
			}

			// Add listener
			// ------------
			static::$listeners[$subevent][] = $listener;

		}


	}

	// Send event
	// ----------
	public static function send($type, $data = null, $options = null) {

		// Store event locally
		// -------------------
		$module = \Core::getModule('events');
		$module->events[] = array('type' => $type, 'data' => $data);

		// All listeners
		// -------------
		if (!empty(static::$listeners['*'])) {
			foreach(static::$listeners['*'] as $listener) {

				// If method, or closure
				// ---------------------
				if (is_array($listener) || is_callable($listener)) {
					call_user_func($listener, $type, $data);
				}

				// Component object
				// ----------------
				if (is_object($listener) && is_a($listener, 'Component')) {
					$listener->dispatchEvent($type, $data);
				}
			}
		}

		// This event listener
		// -------------------
		if (!empty(static::$listeners[$type])) {
			foreach(static::$listeners[$type] as $listener) {

				// If method, or closure
				// ---------------------
				if (is_array($listener) || is_callable($listener)) {
					call_user_func($listener, $type, $data);
				}

				// Component object
				// ----------------
				if (is_object($listener) && is_a($listener, 'Component')) {
					$listener->dispatchEvent($type, $data);
				}

			}
		}

		// To client
		// ---------
		if (@ $options['client'] === true) {
			\Events::send('clientEvent', array('type' => $type, 'data' => $data));
		}

	}

}