<?php

namespace Core\Modules;

class Translate extends \Module {

	public static $component = array(
		'id' => 'translate',
		'title' => 'Перевод и локализация',
		'hasSettings' => true
	);


	// Component settings format
	// -------------------------
	public static $componentSettingsFormat = array(
		'defaultLanguage' => array('type' => 'select', 'title' => 'Язык по-умолчанию', 'allowEmpty' => true, 'values' => array(
			'ru' => 'Русский',
			'en' => 'Английский'
		))
	);

	// Settings
	// --------
	public static $settings = array();

	// Translation table
	// -----------------
	public static $translationTable = array();

	// Override registration
	// ---------------------
	public static function registerComponent() {
		parent::registerComponent();



		// Wait while all components will be initialized
		// ---------------------------------------------
		\Events::addListener('componentInitialized', function($events, $componentClass) {
			if (!empty($componentClass::$componentTranslationTable)) {

				// Get component namespace
				// -----------------------
				$componentNamespace = $componentClass::$component['type'].'-'.$componentClass::$component['id'];

				// Add
				// ---
				\Core::getModule('translate')->addNamespace($componentNamespace, $componentClass::$componentTranslationTable);
			}

		});
	}


	// Add namespace
	// -------------
	public function addNamespace($namespace, $data) {
		static::$translationTable[$namespace] = $data;
	}

	// Translate option
	// ----------------
	public function translate($text, $options = array()) {

		// Detect translation language
		// ---------------------------
		$language = first_var(@ $options['lang'], @ \Core::getApplication()->data['language'], 'en');

		// Get namespace
		// -------------
		$namespace = first_var(@ $options['namespace'], 'default');

		// Get text from table
		// -------------------
		$text = first_var(
			@ static::$translationTable[$namespace][$language][$text],
			$text
		);

		return $text;
	}
}