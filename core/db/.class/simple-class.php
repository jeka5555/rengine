<?php

class SimpleClass extends \Component {


	// Get object's class id
	// ---------------------
	public function getObjectClass() {
		return static::$component['id'];
	}

	// Get object title
	// ----------------
	public function getIdentityTitle() {
		return first_var(@ $this->title, @$this->_id, 'пустой объект');
	}

	// Get full object identity
	// ------------------------
	public function getIdentity() {
		return $this->getIdentityTitle();
	}

	// Constructor
	// -------------------
	public function __construct($args = null) {

		// Create
		// ------
		parent::__construct($args);

		// Require ID
		// ----------
		if (empty($args['_id'])) {
			$this->properties['_id'] = (string) new MongoID();
		}
		// Set an owner
		// ------------
		if (empty($this->properties['@owner'])) {
			if ($user = @ \Core::getModule('users')->user->_id) {
				$this->properties['@owner'] = $user;
			}
		}


	}

	// Read property
	// --------------
	public function get($name = null) {
		if (is_null($name)) return $this->properties;
		return parent::__get($name);
	}


	// Rener content of node
	// ---------------------
	public function render($mode = 'full', $options = array()) {

		// Render widget content
		// ----------------------
		$renderMethod = 'renderMode'.$mode;
		if (method_exists($this, $renderMethod)) {
			$content = call_user_func(array($this, $renderMethod));
			return $content;
		}

		// No any content
		// --------------
		return false;

	}


	public function getClassID() {
		return static::$component['id'];
	}

}
