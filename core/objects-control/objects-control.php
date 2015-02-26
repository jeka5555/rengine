<?php

namespace Core\Modules;


class Objects extends \Module {

	public static $component = array(
		'id' => 'objects-control',
		'title' => 'Средства управления объектами'
	);


	// Apply action to objects
	// -----------------------
	public function actionApplyAction($args = array()) {

		// Require correct data
		// --------------------
		if (empty($args['class']) || empty($args['action'])) return false;

		// Read class
		// ----------
		$class = \Core::getComponent('class', $args['class']);
		if (empty($class)) return false;

		if (!empty($args['objects'])) {
			foreach($args['objects'] as $objectID) {

				// Read and skip empty objects
				// ---------------------------
				$object = $class::findPK($objectID);
				if (empty($object)) continue;

				// Apply action
				// ------------
				$object->action($args['action']);

			}
		}

	}

	// Validate form data
	// ------------------
	public function actionValidate($args = array(), $action) {

		// Parse action
		// ------------
		$actionPath = \URI::parseURI($action);
		$class = @ $actionPath[3];
		if (empty($class)) return false;

		// Need to have an class
		// ---------------------
		$class = \Core::getComponent('class', $class);
		if (empty($class)) return false;

		// Try to load an object
		// ---------------------
		if (!empty($args['_id'])) $object = $class::findPK($args['_id']);
		if (empty($object)) $object = $class::getInstance($args);

		// Validate object
		// ---------------
		$errors = $object->validate();
		return $errors;
	}

	// Save object
	// -----------
	public function actionSave($args = array(), $action) {

		$successURI = null;
		if (!empty($args['formSuccessURI'])) {
			$successURI = $args['formSuccessURI'];
			unset($args['formSuccessURI']);
		}

		// Parse action
		// ------------
		$actionPath = \URI::parseURI($action);
		$class = @ $actionPath[3];
		if (empty($class)) return false;

		// Need to have an class
		// ---------------------
		$class = \Core::getComponent('class', $class);

		if (empty($class)) return false;

		// Try to load an object
		// ---------------------
		if (!empty($args['_id'])) $object = $class::findPK($args['_id']);
		if (empty($object)) $object = $class::getInstance($args);

		// Save
		// ----
		$object->set($args);
		$object->save();

		// Goto success uri
		// ----------------
		if (!empty($successURI)) \Events::send('setLocation', $successURI);

		return true;
	}


}