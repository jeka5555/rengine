<?php

namespace Core\Modules;

class DB extends \Core\Module
{

	public $driver;
	public $connection;

	public $host = 'localhost';
	public $login;
	public $password;
	public $timeout;

	// Register component class
	// ------------------------
	public static function registerComponent()
	{
		parent::registerComponent();
		ini_set('mongo.native_int', 1);
	}

	// Auto connect
	// ------------
	public function initComponent()
	{

		$db = __SITE_ID__;
		$user = @ \Core::getInstance()->dbUser;
		$password = @ \Core::getInstance()->dbPassword;

		// If db isn't set, return
		// -----------------------
		if (empty($db)) {
			\Logs::log('Can\t connect to database. DB name isn\'t valid', 'error');
			return;
		}

		// Create connection
		// -----------------
		$this->driver = new \MongoClient();
		$this->connection = $this->driver->selectDB($db);

		// Authenticate
		// ------------
		if (!empty($user) || !empty($password)) DB::$connection->authenticate($user, $password);

		// Enable fulltext search
		// ----------------------
		$this->connection->command(array("setParameter" => 1, "textSearchEnabled" => true));

	}

}
