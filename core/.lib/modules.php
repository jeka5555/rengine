<?php

class Modules {

	public static $modules = array();

	public static function get($moduleID) {
		return static::$modules[$moduleID];
	}
}