<?php

namespace Core\Modules;

class Components extends \Module {

	// Component
	// ---------
	public static $component = array('id' => 'components', 'title' => 'Компоненты');



	// Get all configurable components
	// -------------------------------
	public function actionGetConfigurableComponents() {

		$result = array();

		$types = & \Component::$componentTypes;
		foreach ($types as $typeID => $componentType) {


			// Get extenisons list
			// -------------------
			$extensionsList = @ \Extension::$ext[$typeID];

			if (!empty($extensionsList))
			foreach ($extensionsList as $extensionID => $extension) {

				// Get extension class
				// -------------------
				$extensionClass = @ $extension['class'];
				if (empty($extensionClass)) continue;

				// Skip non-component classes
				// --------------------------
				if (!is_subclass_of($extensionClass, 'Component')) continue;

				// Skip not configurable components
				// --------------------------------
				if (@ $extensionClass::$component['hasSettings'] != true) continue;

				// Append component
				// ----------------
				$result[$typeID]['components'][$extensionID] = array(
					'id' => $extensionID,
					'title' => first_var(@ $extensionClass::$component['title'], $extensionID),
					'description' => @ $extensionClass::$component['description']
				);
			}


			// Add component type info
			// -----------------------
			if (!empty($result[$typeID])) {
				$result[$typeID]['title'] = first_var( @$componentType::$component['title'], $typeID);
				$result[$typeID]['description'] = @ $componentType::$component['description'];
			}
		}


		// Return result
		// -------------
		return $result;

	}

	// Get component types
	// -------------------
	public function actionGetComponentTypes() {
		$componentTypes = array();

		foreach (\Component::$componentTypes as $type => & $class) {
			$componentTypes[$type] = first_var(@ $class::$component['title'], $type);
		}

		return $componentTypes;
	}


	// Save component settings
	// -----------------------
	public function actionSaveComponentSettings($args = array()) {

		// Guard
		// -----
		if (empty($args['type']) || empty($args['id'])) return;


		$settingsClass = \Core::getClass('component-settings');

		// Create or find settings object
		// ------------------------------        
		$settings = $settingsClass::findOne(array('query' => array('id' => $args['id'], 'type' => $args['type'])));

		if (empty($settings)) {
			$settings = $settingsClass::getInstance($args);
		} else {
			$settings->set($args);
		}

		// Save settings object
		// --------------------
		$settings->save();
		
		// Notify user
		// -----------
		\Core::getModule('flash-messages')->add(array('text' => 'Настройки компонента успешно сохранены!'));

		return true;
	}


	// Save component settings
	// -----------------------
	public function actionClearComponentSettings($args = array()) {

		// Guard
		// -----
		if (empty($args['type']) || empty($args['id'])) return;
		$settingsClass = \Core::getClass('component-settings');

		// Create or find settings object
		// ------------------------------
		$settings = $settingsClass::findOne(array('query' => array('id' => $args['id'], 'type' => $args['type'])));
		if (!empty($settings)) {
			$settings->delete();
		}

		return true;
	}

	// Get component settings format
	// -----------------------------
	public function actionGetComponentEditorFormat($args = array()) {

		// Guards
		// ------
		if (empty($args['type']) || empty($args['id'])) return;

		// Get component
		// -------------
		$component = \Core::getComponent($args['type'], $args['id']);
		if (empty($component)) return;
   
		$componentInstance = $component::getInstance();

		// Default values
		// --------------
		$settingsFormat = array();
		$settings = array();

		// Return
		// ------
		return array(
			'properties' => $properties = $componentInstance->getComponentSettingsFormat(),
			'structure' => $componentInstance->getComponentEditorStructure(),
			'data' => $component::$settings
		);
	}

	// Get components list
	// -------------------
	public function actionGetComponentsList($type) {

		$components = array();

		// Get component class
		// -------------------
		$component = \Core::getComponent('component', $type);
		if (empty($component)) return;


		$components = call_user_func(array($component, 'getComponentsList'));
		//asort($components, SORT_STRING);

		// Return
		// ------
		return $components;
	}
}
