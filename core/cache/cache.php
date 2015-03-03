<?php

namespace Core\Cache\Modules;

class Cache extends \Module {

	public static $component = array(
		'id' => 'cache',
		'title' => 'Кэш'
	);

	// Make cache key
	// --------------
	public function makeKey($options = null) {

		$opt = array();

		// Create unique key
		// -----------------
		if (@ $options['uri'] == true) $opt['uri'] = $_SERVER['REQUEST_URI'];
		if (@ $options['user'] == true) $opt['user'] = @ \Core::getModule('users')->user->_id;

		$result = md5(serialize($opt));
		if (!empty($options['id'])) $result = $options['id'].'-'.$result;
		return $result;
	}

	// Is data exists
	// --------------
	public function exists($options = null, $expiration = 0, $removeExpiried = false) {

		// Get cache class
		// ---------------
		$cacheRecordClass = \Core::getComponent('class', 'cache-record');

		// Get key
		// --------
		$key = $this->makeKey($options);

		// Check for count
		// ---------------
		$object = $cacheRecordClass::findOne(array('query' => array('key' => $key)));
		if (empty($object)) return false;

		// Check for expiration
		// --------------------
		if (($object->expiration + $expiration) > time()) {
			return true;
		}

		// Expiried
		// --------
		else {

			// Remove expiried
			// ---------------
			if ($removeExpiried == true) {
				$object->delete();
			}
			return false;
		}


	}

	// Push data to cache
	// ------------------
	public function push($data = null, $options = array(), $expiration = 10000000) {

		// Cache record class
		// ------------------
		$cacheRecordClass = \Core::getComponent('class', 'cache-record');

		// Create record
		// -------------
		$object = $cacheRecordClass::getInstance();

		// Fill it with data
		// -----------------
		$object->data = $data;
		$object->key = $this->makeKey($options);;
		$object->expiration = time() + $expiration;

		// Save object
		// -----------
		$object->save();

	}

	// Pop data from cache
	// -------------------
	public function pop($options = array(), $expiration = 10000000) {

		// Make key and connection
		// -----------------------
		$key = $this->makeKey($options);
		$cacheRecordClass = \Core::getComponent('class', 'cache-record');

		// Check for old records
		// ---------------------
		$result = $cacheRecordClass::findOne(array('query' => array('key' => $key)));
		if (empty($result)) return false;

		// Return data
		// -----------
		return $result->data;

	}

}
