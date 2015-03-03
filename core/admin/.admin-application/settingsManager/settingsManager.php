<?php

namespace Core\Admin\AdminApplications;

class SettingsManager extends \Core\Admin\Components\AdminApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'settingsManager',
		'title' => 'Настройки',
		'addOnToolbar' => true,
		'icon' => '/core/admin/.admin-application/settingsManager/settings.png',
		'access' => array(
			array('type' => 'userRole', 'role' => 'administrator')
		)
	);

	// Save object settings
	// --------------------
	public function commandSaveComponentSettings($args = array()) {

		// Need something
		// --------------
		if (empty($args['type']) || empty($args['id'])) return false;

		$settingsClass = \Core::getComponent('class', 'settings');
		$settings = $settingsClass::findOne(array('query' => array('type' => $args['type'], 'id' => $args['id'])));

		// If settings are exists
		// ----------------------
		if (!empty($settings)) {
			$settings->properties = $args['settings'];
			$settings->save();
			return true;
		}

		// Or create new one
		// -----------------
		else {
			$settings = $settingsClass::getInstance($args['settings']);
			$settings->save();
		}

		return true;

	}

	// Open data for components
	// ------------------------
	public function commandGetComponentForm($args = array()) {

		// Get component
		// -------------
		$component = @ \Components::$types[$args['type']][$args['id']];
		if (empty($component)) return false;

		// Return component data
		// ---------------------
		return array(
			'form' => array(
				'format' => $component::getComponentSettingsFormat(),
				'object' => $component::getComponentSettings(),
			)
		);

	}

	// Init manager with data
	// ----------------------
	public function commandInit($args = array()) {

		// Collect data here
		// -----------------
		$components = array(
			'module' => array('title' => 'Модули', 'components' => array()),
			'widget' => array('title' => 'Виджеты', 'components' => array()),
			'application' => array('title' => 'Приложения управления', 'components' => array()),
		);

		// Get list of editable components
		// -------------------------------
		foreach($components as $type => $component) {

			// Read extensions of this type
			// ----------------------------
			$extensions = @ \Components::$types[$type];
			if (empty($extensions)) continue;

			// Add extensions with settings
			// ----------------------------
			foreach($extensions as $extID => $ext) {

				$extClass = \Core::getComponent($type, $extID);

				if (@ $extClass::$component['hasSettings'] !== true) continue;
				$components[$type]['components'][$extID] = array(
					'id' => $extID,
					'title' => first_var(@ $extClass::$component['title'], $extID),
					'description' => @ $extClass::$component['description']
				);
			}

			// If no any components found, remove group
			// ----------------------------------------
			if (empty($components[$type]['components'])) unset($components[$type]);

		}

		// Return list of components
		// -------------------------
		return $components;


	}


	// Save core
	// ---------
	public function commandSaveCoreSettings($settings = array()) {
        return file_put_contents(__DR__.'/config.php', '<?php $config = ' . var_export($settings, true) . ';');
	}

	// Get core settings
	// -----------------
	public function commandGetCoreSettings() {
		return \Core::$settings;
	}


}