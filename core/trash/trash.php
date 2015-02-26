<?php

namespace Core\Modules;

class Trash extends \Module {


	public static $component = array(
		'id' => 'trash',
		'title' => 'Мусорная корзина'
	);


	// Settings
	// --------
	public static $settings = array(
		'useTrash' => true
	);

	// Clear trashbin
	// --------------
	public function emptyTrash() {
	}

	// Restore object
	// --------------
	public function restore($id = null) {
	}

	// Put object to trashbin
	// ----------------------
	public function put($args = array()) {

		$trashClass = \Core::getClass('trash');
		$trashObject = $trashClass::getInstance($args);
		return $trashObject->save();
	}
}
