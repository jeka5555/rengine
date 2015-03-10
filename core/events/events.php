<?php

namespace Core\Modules;

class Events extends \Core\Module
{

	// Events storage
	// --------------
	public $events = array();

	// Events listeners
	// -----------------
	public $listeners = array('*' => array());

	// Add listener
	// ------------
	public function addListener($event, $listener)
	{

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
			if (!isset($this->listeners[$subevent]) || !is_array($this->listeners[$subevent])) {
				$this->listeners[$subevent] = array();
			}

			// Add listener
			// ------------
			$this->listeners[$subevent][] = $listener;

		}

	}

	// Send event
	// ----------
	public function send($type, $data = null, $options = null)
	{

		$this->events[] = array('type' => $type, 'data' => $data);

		// All listeners
		// -------------
		if (!empty($this->listeners['*'])) {
			foreach ($this->listeners['*'] as $listener) {

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
		if (!empty($this->listeners[$type])) {
			foreach ($this->listeners[$type] as $listener) {

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
			$this->send('clientEvent', array('type' => $type, 'data' => $data));
		}

	}

}