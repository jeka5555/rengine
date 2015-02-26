<?php

class AccessableClass extends SimpleClass {


	// Class access rules
	// ------------------
	public static $classAccessRules = null;

	// Access to single field
	// ----------------------
	public function checkPropertyAccess($field, $action = null) {

		// Get format
		// ----------
		$format = $this->getClassProperties();

		// If have some rules, evaluate
		// ----------------------------

		if (!empty($format[$field]['access'])) {
			return \Rules::check($format[$field]['access']);
		}

		// Or - true
		// ---------
		return true;
	}


	// Check access for single action
	// ------------------------------
	public function checkAccess($action = null) {

		$accessAction = first_var($action, 'default');

		// Owner has all grants
		// --------------------
		if ($user = @ \Core::getModule('users')->user) {
			if (@$this->properties['@owner'] === $user->_id) return true;
		}

		// Access properties are overriden
		// -------------------------------
		if ($access = @$this->properties['@access']) {
			if (!empty($access[$accessAction]))
				return \Rules::check($access[$accessAction]);
		}

		// Of check class action access
		// ----------------------------
		return static::checkClassAccess(@$accessAction);

	}

	// Check access to action on class-based rule
	// ------------------------------------------
	public static function checkClassAccess($action = null) {

		// Detect action
		// --------------
		$accessAction = first_var($action, 'default');

		// Rules we will check
		// -------------------
		$accessRules = first_var(
			@ static::$classAccessRules[$accessAction],
			@ static::$classAccessRules['default']
		);

		// If no any, return true
		// ----------------------
		if (empty($accessRules)) return true;

		// If we have something, check it
		// ------------------------------
		$result = \Rules::check($accessRules);

		// Если ничего не задано, то разрешено
		// -----------------------------------
		if ($result == false) return $result;
		return true;

	}

}
