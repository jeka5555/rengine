<?php

namespace Core\Components;


class ObjectEditor extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'object-editor',
		'title' => 'Редактор объектов',
		'autoload' => true
	);

	public $object = null; // Object to edit
	public $data = array(); // Editable object
	public $structure = array(); // Editor structure
	public $properties = array(); // Object properties

	// Apply mapping
	// -------------
	public function applyPropertiesMapping($element, $path = array()) {


		// Apply single property
		// ---------------------
		if (!empty($element['property'])) {
			$this->mapProperty($element['property'], $path);
		}

		// Apply properties list
		// ---------------------
		if (!empty($element['properties'])) {

			foreach ($element['properties'] as $propertyID) {
//				$this->mapProperty( $propertyID, $path);
			}
		}

		// Apply properties to subelements
		// -------------------------------
		if (!empty($element['elements'])) {
			foreach ($element['elements'] as $elementID => $element) {

				// Add new element to path
				// -----------------------
				$newPath = $path;
				$newPath[] = $elementID;

				// Execute mapping
				// ---------------
				$this->applyPropertiesMapping($element, $newPath);
			}
		}

	}



	// Get properties ob an objec which are editable
	// ---------------------------------------------
	public function getObjectProperties() {

		// Get object's properties
		// -----------------------
		$propertiesList = $this->object->getClassProperties();

		// Guard
		// -----
		if (empty($propertiesList)) return array();

		// Check access data
		// -----------------
		foreach($propertiesList as $propertyID => $property) {
			if (!empty($property['access'])) {

				// Remove properties which aren't accessable
				// -----------------------------------------
				if (!\Rules::check($property['access'])) {
					unset($propertiesList[$propertyID]);
				}
			}
		}

		return $propertiesList;

	}


	// Open editor
	// -----------
	public function open() {


		// Set editor id
		// -------------
		$editorID = 'editor'.uniqid();

		// Open object editor
		// ------------------
		$editorJSClass = 'objectEditor';

		// Get object class
		// ----------------
		$objectClass = @ get_class($this->object);
		$classID = $objectClass::$component['id'];

		// Can we edit this type of objects?
		// ---------------------------------
		if (!$objectClass::checkClassAccess('edit')) {
			\Core::getModule('flash-messages')->add(array(
				'type' => 'error',
				'text' => 'Вы не можете редактировать объекты класса '. first_var(@ $objectClass::$component['title'], $objectClass::$component['id'])
			));
		}

		// Can we edit this object?
		// ------------------------
		if (!empty($this->object) && !$this->object->checkAccess('edit')) {
			\Core::getModule('flash-messages')->add(array(
				'type' => 'error',
				'text' => 'Вы не можете редактировать объект '. $this->object->getIdentity
			));
		}

		// Get properties of an object
		// ---------------------------
		$this->properties = $this->getObjectProperties();


		// Filter object's data
		// --------------------
		$this->data = array();
		foreach($this->properties as $propertyID => $property) {
			if (isset($this->object->properties[$propertyID])) {
				$this->data[$propertyID] = $this->object->properties[$propertyID];
			}
		}

		// Build editor structure
		// ----------------------
		$this->structure = $this->object->getEditorStructure();

		// Build editor args
		// -----------------
		$editorArgs = array(

			// JS Controller
			// -------------
			'jsController' => @ $this->object->editorJSController,

			// Title for editor window
			// -----------------------
			'title' => 'Редактирование '.$this->object->getIdentity(),

			// Object properties
			// -----------------
			'id' => @ $this->object->_id,
			'class' => $classID,

			// Editor data
			// -----------
			'data' => $this->data,
			'elements' => $this->structure,
			'properties' => $this->properties,


			// Save URI
			// --------
			'saveURI' => '/module/objects/save',
		);

		// Create
		// ------
		\Events::send('addScript',' '.$editorID.' = Apps.start("'.$editorJSClass.'", '.json_encode($editorArgs, 1).');');
		return $editorID;

	}


	// Get editor structure
	public function getStructure() {

	// Set editor id
		// -------------
		$editorID = 'editor'.uniqid();

		// Open object editor
		// ------------------
		$editorJSClass = 'objectEditor';

		// Get object class
		// ----------------
		$objectClass = @ get_class($this->object);
		$classID = $objectClass::$component['id'];

		// Can we edit this type of objects?
		// ---------------------------------
		if (!$objectClass::checkClassAccess('edit')) {
			\Core::getModule('flash-messages')->add(array(
				'type' => 'error',
				'text' => 'Вы не можете редактировать объекты класса '. first_var(@ $objectClass::$component['title'], $objectClass::$component['id'])
			));
		}

		// Can we edit this object?
		// ------------------------
		if (!empty($this->object) && !$this->object->checkAccess('edit')) {
			\Core::getModule('flash-messages')->add(array(
				'type' => 'error',
				'text' => 'Вы не можете редактировать объект '. $this->object->getIdentity
			));
		}

		// Get properties of an object
		// ---------------------------
		$this->properties = $this->getObjectProperties();


		// Filter object's data
		// --------------------
		$this->data = array();
		foreach($this->properties as $propertyID => $property) {
			if (isset($this->object->properties[$propertyID])) {
				$this->data[$propertyID] = $this->object->properties[$propertyID];
			}
		}

		// Build editor structure
		// ----------------------
		$this->structure = $this->object->getEditorStructure();

		// Build editor args
		// -----------------
		$editorArgs = array(

			// JS Controller
			// -------------
			'jsController' => @ $this->object->editorJSController,

			// Title for editor window
			// -----------------------
			'title' => 'Редактирование '.$this->object->getIdentity(),

			// Object properties
			// -----------------
			'id' => @ $this->object->_id,
			'class' => $classID,

			// Editor data
			// -----------
			'data' => $this->data,
			'elements' => $this->structure,
			'properties' => $this->properties,

		);


		return $editorArgs;

	}
}
