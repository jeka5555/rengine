<?php

namespace Core\Admin\AdminApplications;

class ObjectEditor extends \Core\Admin\Components\AdminApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'objectEditor',
		'title' => 'Редактор объектов',
		'access' => array(
			array('type' => 'userRole', 'role' => 'administrator')
		)
	);



	// Init editor data
	// ----------------
	public function commandInit($args = array()) {

		// Guard
		// -----
		if (empty($args['class'])) return null;

		// Get class
		// --------------------------------------
		$class = @\Core::getComponent('class', $args['class']);
		if (empty($class)) return;

		// Try to load an object
		// ---------------------
		if (!empty($args['id'])) {
			$object = $class::findPK($args['id']);
		}

		// Get editor component
		// --------------------
		$editorComponent = \Core::getComponent('component', 'object-editor');
		if (empty($editorComponent)) return;
		$editor = $editorComponent::getInstance(array( 'object' => $object));

		// Open editor
		// -----------
		$editor->open();

	}


}
