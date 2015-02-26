<?php

namespace Core\FlashMessages\Widgets;

class FlashMessages extends \Widget {

	// Component definition
	// --------------------
	public static $component = array('id' => 'flash-messages');


	// Render component
	// ----------------
	public function get() {

		// Collect all messages
		// --------------------
		if (!empty($_SESSION['flashMessages'])) {

			// Collect messages content
			// ------------------------
			$content = '';
			foreach($_SESSION['flashMessages'] as $message) {
				$type = first_var(@ $message['type'], '');
				$content .= '<div class="flash-message '.$type.'">'.@ $message['text'].'</div>';
			}

			// Wrap content
			// ------------
			$content = '<div class="flash-messages">'.$content.'</div>';

			// Popup data
			// ----------
			$popupData = array(
				'title' => 'Сообщения',
				'content' => $content
			);

			// Build a script
			// --------------
			$popupID = 'popup'.uniqid();
			\Events::send('addScript', 'var '.$popupID.' = new Popup('.json_encode($popupData).');');
			\FlashMessages::clear();

		}

	}
}
