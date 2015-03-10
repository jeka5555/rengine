<?php

namespace Core\Modules;

class Users extends \Core\Module
{

	// Current user
	// ------------
	public $user;

	// Login properties
	// ----------------
	public $allowLogin = true;
	public $loginFormat = 'email-or-phone';
	public $passwordMinLength;

	// Registration properties
	// -----------------------
	public $allowRegistration = true;
	public $registerConfirmationRequired = true;
	public $registerConfirmationType = 'email';

	// Users collection
	// ----------------
	public $usersCollection = 'users';

	// User logout
	// -----------
	public function logout($args = array())
	{

		// Log
		// ---
		\Logs::log(array('action' => 'logout', 'message' => 'User was logged off.'), 'access');

		// Remove session data
		// -------------------
		unset($_SESSION['sessionID']);

		// Save user session ID
		// --------------------
		if (!empty($this->user)) {
			$this->user->set('sessionID', null);
			$this->user->save();
		}

		// Event
		// -----
		\Events::send('logout');
		\Events::send('setLocation', '/');

	}

	// Init module
	// -----------
	public function init()
	{

		// Login user from session
		// -----------------------
		if (!isset($_SESSION['sessionID']) and isset($_COOKIE['sessionID']))
			$_SESSION['sessionID'] = $_COOKIE['sessionID'];

		// If no any session, exit
		// -----------------------
		if (!isset($_SESSION['sessionID'])) return false;

		// Read session user
		// -----------------
		$usersCollection = \Core::getComponent($this->usersCollection);
		$user = $usersCollection::findOne(array('query' => array('sessionID' => $_SESSION['sessionID'], 'isActive' => true)));

		// Set user
		// --------
		$this->user = $user;
		$this->userID = @$user->_id;

	}

	// Login
	// -----
	public function login($args = array())
	{

		// Nothing
		// -------
		if (empty($args['email']) || empty($args['password'])) return;

		// Get user class
		// --------------
		$usersCollection = \Core::getComponent($this->usersCollection);

		// Read user
		// ---------
		$user = $usersCollection::findOne(array('query' => array('primaryEmail' => $args['email'], 'password' => md5(@ $args['password']), 'isActive' => true)));

		// If no user, log error
		// ---------------------
		if (empty($user)) {
			\Events::send('message', array('text' => 'User isn\'t exists', 'type' => 'error'));
			return false;
		}

		// Logout old user
		// ---------------
		if (!empty($this->user)) $this->logout();

		// Set new user
		// ------------
		$this->user = $user;

		// Create session identifier
		// -------------------------
		$sessionID = md5(uniqid());
		$_SESSION['sessionID'] = $sessionID;
		$user->set('sessionID', $sessionID);
		$user->save();
		setcookie("sessionID", $sessionID, time() + 315360000, "/");

		// Log
		// ---
		\Logs::log(array('action' => 'loginSuccess', 'message' => 'User logged in', 'sessionID' => @ $sessionID,), 'access');

		// Finish
		//  -----
		\Events::send('login');
		return true;
	}
}