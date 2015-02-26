<?php

class Blocks {

	// Вывод блока
	// -----------
	public static function get($blockID, $args = array(), $options = array()) {
		$args['id'] = $blockID;
		return \Widgets::get('block', $args, $options);
	}
}