<?php


class FlashMessages extends \Module {

	// Define component
	// ----------------
	public static $component = array(
		'id' => 'flash-messages',
		'title' => 'Сообщения для пользователя'
	);


	public $messages = array(); // Store messages here

	// Init component
	// --------------
	public static function initComponent() {

		// Init parent
		// -----------
		parent::initComponent();

		// Prepare messages container
		// --------------------------
		if (!isset($_SESSION['flashMessages']) || !is_array($_SESSION['flashMessages'])) {
			$_SESSION['flashMessages'] = array();
		}

		// Link to session
		// ---------------
		\Core::getModule('flash-messages')->messages = & $_SESSION['flashMessages'];

		// Read messages from buffer
		// -------------------------
		\Events::addListener('applicationRouteFinish', function() {

			// Ignore JSON requests
			// --------------------
//			if (@ \Core::getApplication()->request->isAJAX == true) return;

			// Get messages list
			// -----------------
			$messages = & \Core::getModule('flash-messages')->messages;

			// If some messages are exists
			// ---------------------------
			if (!empty($messages)) {

				// Add each one to output
				// ----------------------
				foreach($messages as $message) {

					// Check visibility
					// ----------------
					if (!empty($message['visibility'])) {
						if (! \Rules::check($message['visibility'])) continue;
					}

					// Add
					// ---
					$messageScript = 'FlashMessages.add('.json_encode($message, 1).')';
					\Events::send('addEndScript', $messageScript);
				}
			}

		});

		// Clear messages
		// --------------
		\Events::addListener('applicationFinish', function() {
			\Core::getModule('flash-messages')->clear();
		});

	}

	// Add new flash message
	// ---------------------
	public function add($message = array()) {
		$_SESSION['flashMessages'][] = $message;
	}

	// Clear flash messages
	// --------------------
	public function clear() {
		$_SESSION['flashMessages'] = array();
	}
}