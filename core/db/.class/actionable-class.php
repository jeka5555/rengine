<?php
class ActionableClass extends SearchableClass {


	// Class actions
	// -------------
	public static $classActions = array();
	public static $selectorJSClass = 'objectsManager';

	// Execute action over the object
	// ------------------------------
	public function doAction($action = null, $data = array()) {

		// If object is deleted, no any action could be applied
		// ----------------------------------------------------
		if (isset($this->properties['@deleted'])) return;

		// Find method
		// -----------
		$method = 'action'.ucfirst($action);
		if (!method_exists($this, $method)) return false;

		// Check method access
		// -------------------
		if (!$this->checkAccess($method)) return false;

		// Call method
		// -----------
		return call_user_func(array($this, $method), $data);

	}

	// Get class actions in runtime
	// ----------------------------
	public static function getClassActions() {
		return @ static::$classActions;
	}

	// Get list of actions
	// -------------------
	public static function getClassActionsList() {

		$actions = array();
		if (!empty(static::$classActions)) {
			foreach(static::$classActions as $actionID => $action) {

				// Skip individual actions
				// -----------------------
				if (@ $action['bulkAction'] != true && @ $action['classAction'] != true) continue;

				// Check for access
				// ----------------
				$access = static::checkClassAccess('action'.ucfirst($actionID));
				if (!$access) continue;

				// Add to list
				// -----------
				$actions[] = array(
					'id' => $actionID,
					'title' => first_var(@ $action['title'], $actionID),
					'classAction' => @ $action['classAction'],
					'bulkAction' => @ $action['bulkAction'],
				);

			}
		}

		// Return list of actions
		// ----------------------
		return $actions;

	}

	// Спсиок дейстивй для объекта
	// ---------------------------
	public function actionGetActionsList($args = array()) {

		// Collect here
		// ------------
		$actions = array();

		// Itterate
		// --------
		if (!empty(static::$classActions)) {
			foreach(static::$classActions as $actionID => $action) {

				// Skip class actions
				// ------------------
				if (@ $action['classAction'] == true) continue;

				// Filter not allowed actions
				// --------------------------
				if (!empty($args['allowedActions']) && !in_array($actionID, $args['allowedActions'])) continue;

				// Check access to this action
				// ---------------------------
				$access = $this->checkAccess('action'.ucfirst($actionID));
				if (!$this->checkAccess('action'.ucfirst($actionID))) continue;

				// Add to list
				// -----------
				$actionData = $action;
				$actionData['action'] = $actionID;
				if (empty($actionData['title'])) $actionData['title'] = $actionID;
				$actions[] = $actionData;

			}
		}

		// If it's owner or admin, they can change access
		// ----------------------------------------------
		if ($this->get('@owner') == @ \Core::getModule('users')->user->_id || \Rules::checkRule(array('type' => 'userRole', 'role' => 'administrator'))) {
			$actions[] = array('action' => 'editAccess', 'title' => 'Права доступа');
		}

		return $actions;
	}


	// Save
	// ----
	public static function classActionSave($args = array()) {
	}

	// Create object picker
	// --------------------
	public static function classActionPickObject($args = array()) {

		// Detect selector class
		// ----------------------

		$selectorJSClass = static::$selectorJSClass;

		// Generate selector ID
		// --------------------
		$selectorID = 'selector'.uniqid();

		// Build selector args
		// -------------------
		$selectorArgs = array_merge($args, array(
			'class' => static::$component['id'],
			'selectMode' => true,
			'multiselect' => first_var(@ $args['multiselect'], false),
		));

		// Call selector
		// -------------
		\Events::send('addScript', $selectorID.' = new Apps.start("'.$selectorJSClass.'", '.json_encode($selectorArgs, 1).');');

		// Return selector ID
		// ------------------
		return $selectorID;
	}

	// Create new object
	// -----------------
	public static function classActionCreate($args = array()) {

		// Create empty object
		// -------------------
		$object = static::getInstance();
		$editorID = $object->actionEdit(array('data' => @ $args['data']));
		return $editorID;
	}


	// Drop actions
	// ------------
	public function actionDrop($objectData) {

		if (!empty($objectData)) {
			foreach($objectData as $contextID => $contextData) {
				$this->doAction('drop'. $contextID, $contextData);
			}
		}
	}

	// Save object
	// -----------
	public static function classActionCreateObject($data = array()) {

		// Create and save
		// ---------------
		$object = static::getInstance();

		// Set data
		// --------
		if (!empty($data['data'])) {
			$object->safeSet($data['data']);
		}

		$object->save();

		// Return ID
		// ----------
		return $object->_id;

	}


	// Drop object
	// -----------
	public function actionDropObject($object = array()) {
		if (empty($object['class']) || empty($object['id'])) return;
		$action = 'drop' . $object['class'];
		return $this->doAction($action, $object);
	}

	// Get information about object
	// ----------------------------
	public function actionGetInfo() {
		return array(
			'className'	=> first_var(static::$component['title'], static::$component['id']),
			'identity' => $this->getIdentity(),
			'actions' => $this->actionGetActionsList(),
		);
	}

	// Update object
	// --------------
	public function actionUpdate($data = null) {

		if (!empty($data)) {
			foreach($data as $propertyID => $value) {
				$this->set($propertyID, $value);
			}
		}

		$this->save();
		return true;
	}

	// Edit object
	// -----------
	public function actionEdit($args = array()) {

		// Set overrided data
		// ------------------
		if (!empty($args['data']) && is_array($args['data'])) {
			$this->set($args['data']);
		}

		// Get editor component
		// --------------------
		$editorComponent = \Core::getComponent('object-editor', 'generic');
		if (empty($editorComponent)) return;

		// Create editor
		// -------------
		$editor = $editorComponent::getInstance();
		$editor->object = $this;

		// Open editor
		// -----------
		return $editor->open();


	}

	// Delete object
	// -------------
	public function actionDelete() {
		return $this->delete();
	}

	// Do mass actions or global class actions
	// ---------------------------------------
	public function doClassAction($action, $data = array()) {

		// Need real action
		// ----------------
		if (empty($action)) return;

		// Get actions
		// -----------
		$actions = static::getClassActions();

		// Get action
		// ----------
		$actionData = @ $actions[$action];

		if (empty($actionData)) return;

		// Process class action
		// --------------------
		if (@ $actionData['classAction'] == true) {

			// Get action method
			// -----------------
			$method = 'classAction'.$action;
			if (!method_exists(get_called_class(), $method)) return false;

			// Check access
			// ------------
			if (!static::checkClassAccess('classAction'.ucfirst($action))) return false;

			// Execute
			// -------
			return call_user_func(array(get_called_class(), $method), $data);

		}

		// Process bulk action
		// -------------------
		else if (@ $actionData['bulkAction'] == true) {

			// Process list or objects
			// -----------------------
			if (!empty($data['objects']) && is_array($data['objects'])) {

				// Itterate each object and apply action to it
				// -------------------------------------------
				foreach($data['objects'] as $objectID) {

					// Read object
					// -----------
					$object = @ static::safeFindPK($objectID);
					if (empty($object)) continue;

					// Apply action
					// ------------
					$object->doAction($action);
				}
			}
		}

		return true;

	}

	// Clone object
	// ------------
	public function actionClone() {

		// Create object
		// -------------
		$class = get_called_class();
		$clone = $class::getInstance();

		// Copy props
		// ----------
		$clone->properties = $this->properties;

		// Add new Id
		// ----------
		$clone->_id = (string) new MongoID();
		$clone->save();

		// Open to edit
		// ------------
		$clone->actionEdit();
		return true;
	}


	// Edit object's access
	// --------------------
	public function actionEditAccess() {
		\Events::send('addScript', 'new Apps.start("objectAccessManager", { id : "'.$this->_id.'", class : "'.static::$component['id'].'"});');
	}

	// Init actions
	// ------------
	public static function initComponent() {

		// Init parent class
		// -----------------
		parent::initComponent();

		// Get parent class
		// ----------------
		$parentClass = get_parent_class(get_called_class());

		// Any actions?
		// ------------
		if (!empty($parentClass)) {
			if (!empty($parentClass::$classActions)) {
				static::$classActions = array_merge($parentClass::$classActions, static::$classActions);
			}
		}

	}

	// Get identity
	// ------------
	public function actionGetIdentity() {
		return $this->getIdentity();
	}

	// Get object preview
	// ------------------
	public function actionGetPreview() {
		return '<div>'.$this->getIdentity().'</div>';
	}


}
