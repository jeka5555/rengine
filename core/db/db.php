<?php

ini_set('mongo.native_long', 1);
ini_set('mongo.native_int',1);

// Основной класс обеспечивающий соединение с базой данных
// -------------------------------------------------------
class DB extends \Module {

	// Свойства
	// --------
	public static $driver; // Основное подключение
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
		static::connect(array('db' => __SITE_ID__));
	}

	// Подключние к источнику
	// ---------------------
	public static function connect($args = array()) {

		// База должна быть передана
		// -------------------------
		if (empty($args['db'])) \Logs::log('Ошибка подключения к базе данных, не указано имя', 'error');

		// Создаем соединение
		// ------------------
		DB::$driver = new MongoClient();
		DB::$connection = DB::$driver -> selectDB($args['db']);

		// Включение полнотекстового поиска
		// --------------------------------
		DB::$connection->command(array("setParameter" => 1, "textSearchEnabled" => true));


		// Если есть параметры подключения, используем их
		// ----------------------------------------------
		if (!empty($args['user']) || !empty($args['password'])) {
			DB::$connection->authenticate($args['user'], $args['password']);
		}
	}
}
