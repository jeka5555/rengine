<?php

ini_set('mongo.native_int',1);

class DB extends \Module {

	public static $driver;
	public static $connection;

	// Component
	// ---------
	public static $component = array(
		'id' => 'db',
		'title' => 'Базы данных',
		'description' => 'Модуль осуществляющий поддержку соединения с базой данных',
	);

	// Settings
	// --------
	public static $settings = array(
		'host' => 'localhost',
		'login' => null,
		'password' => null,
		'timeout' => null
	);

	// Auto connect
	// ------------
	public static function initComponent() {

		$db = __SITE_ID__;
		$user = @ \Core::$settings['dbUser'];
		$password = @ \Core::$settings['dbPassword'];

		// If db isn't set, return
		// -----------------------
		if (empty($db)) {
			\Logs::log('Ошибка подключения к базе данных, не указано имя', 'error');
			return;
		}

		// Create connection
		// -----------------
		DB::$driver = new MongoClient();
		DB::$connection = DB::$driver->selectDB($db);

		// Authenticate
		// ------------
		if (!empty($user) || !empty($password)) DB::$connection->authenticate($user, $password);

		// Enable fulltext search
		// ----------------------
		DB::$connection->command(array("setParameter" => 1, "textSearchEnabled" => true));

	}

}
