<?php

namespace Core\UserPages;

class Payments extends \UserPage{

	// Component
	// ---------
	public static $component = array(
		'id' => 'payments',
		'title' => 'Платежи',
		'hasSettings' => true
	);

	// Settings
	// --------
	public static $settings = array(
	);
}