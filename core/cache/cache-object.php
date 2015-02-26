<?php

namespace Core\Cache\Classes;

class CacheRecord extends \ObjectClass {

	// Component
	// ---------
	public static $component = array( 'id' => 'cache-record', 'title' => 'Запись кэша');

	// Class properties
	// ---------------
	public static $classProperties = array(
		array('id' => 'id', 'type' => 'text', 'listing' => true, 'sortable' => true),
		array('id' => 'data', 'type' => 'generic'),
		array('id' => 'expiration', 'type' => 'datetime', 'title' => 'Дата устаревания', 'listing' => true, 'sortable' => true),
	);

}