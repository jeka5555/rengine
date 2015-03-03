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

		$types = &\Components::$types;
		foreach ($types as $typeID => $componentType) {
			if ($typeID == 'all') continue;

			// Get extenisons list
			// -------------------
			$componentsList = @ \Components::$types[$typeID];

			if (!empty($componentsList))
				foreach ($componentsList as $componentID => $component) {

					if (empty($component)) continue;

				// Skip non-component classes
				// --------------------------
					if (!is_subclass_of($component, 'Component')) continue;

				// Skip not configurable components
				// --------------------------------
					if (@ $component::$component['hasSettings'] != true) continue;

				// Append component
				// ----------------
					$result[$typeID]['components'][$componentID] = array(
						'id' => $componentID,
						'title' => first_var(@ $component::$component['title'], $componentID),
						'description' => @ $component::$component['description']
				);
			}

			// Add component type info
			// -----------------------
			if (!empty($result[$typeID])) {
				$typeComponent = \Core::getComponent('component', $typeID);
				$result[$typeID]['title'] = first_var(@$typeComponent::$component['title'], $typeID);
				$result[$typeID]['description'] = @ $typeComponent::$component['description'];
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

		if (empty($settings)) $settings = $settingsClass::getInstance($args);
		else $settings->set($args);

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

		foreach (\Components::$types['type'] as $component) {
			$components[] = array(
				'title' => @ $component::$component['title'],
				'id' => @ $component::$component['id'],
				'description' => @ $component::$component['description'],
				'type' => @ $component::$component['type']
			);
		}

		//asort($components, SORT_STRING);

		// Return
		// ------
		return $components;
	}
}
