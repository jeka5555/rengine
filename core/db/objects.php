<?php

namespace Modules;

class Objects extends \Module {

	// Инициализация компонента
	// ------------------------
	public static $component = array(
		'id' => 'objects',
		'title' => 'Объекты'
	);


	// Save object
	// -----------
	public function actionSave($data, $path) {
  	
		// Guard
		// -----
		if (empty($data['class'])) return false;

		// Get class definition
		// --------------------
		$class = \Core::getClass($data['class']);
		if (empty($class)) return;

		// Save
		// ----
		return $class::safeFindAndSave($data['data']);

	}

	// Save object
	// -----------
	public function actionPickObject($data, $path) {

		// Guard
		// -----
		if (!empty($data['class'])) {

			// Get class definition
			// --------------------
			$class = \Core::getClass($data['class']);
			if (empty($class)) return;
			
			// Save
			// ----
			return $class::classActionPickObject(@ $data);
				
		} else {
		
			\ObjectClass::classActionPickObject(@ $data);
		} 



	}

	// Attach object's controller
	// --------------------------
	public function attachController($args = array()) {

		// Guard
		// -----
		if (empty($args['class']) || empty($args['id'])) return;


		// Get object
		// ----------
		$objectClass = \Core::getClass($args['class']);
		$object = $objectClass::findPK($args['id']);
		if (empty($object)) return;

		// Attach only for edit mode
		// -------------------------
		if (
			@ \Core::getApplication()->data['editMode'] !== true
			|| ! $object->checkAccess('edit')
		) return false;

		// Arguments
		// ---------
		$args = array(
			'class' => $args['class'],
			'id' => $args['id'],
			'widget' => $args['widget'],
			'useWrapper' => true
		);

		// Add script
		// ----------
		\Events::send('addEndScript', 'new ObjectController('.json_encode($args).');');

	}


	// Общий роутер
	// ------------
	public function action($action, $data = array(), $path = null) {


		// Override
		// --------
		if (in_array($action, array(
			'save',
			'pickObject'
		))) {
			return parent::action($action, $data, $path);
		}


		// Single object's action
		// ----------------------
		if ($action != 'classAction') {

			// Get action
			// ----------
			$class = @ $path[2];
			$action = @ $path[4];
			$objectID = @ $path[3];

			// Full set of properties
			// ----------------------
			if (empty($class) || empty($action) || empty($objectID)) return;

			// Get class
			// ---------
			$classObject = \Core::getComponent('class', $class);
			if (empty($classObject)) return;

			// Get object
			// ------------------
			$object = $classObject::safeFindPK($objectID);
			if (empty($object)) return;

			return @call_user_func(array($object, 'doAction'), $action, $data);
		}

		// Class action
		// ------------
		else {

			$class = @ $data['class'];
			$action = @ $data['action'];


			// Full set of properties
			// ----------------------
			if (empty($class) || empty($action)) return;

			// Get class
			// ---------
			$classObject = \Core::getComponent('class', $class);
			if (empty($classObject)) return;

			// Apply action
			// ------------
			$classObject = new $classObject();
			return call_user_func(array($classObject, 'doClassAction'), $action, @ $data);

		}
	}

}
