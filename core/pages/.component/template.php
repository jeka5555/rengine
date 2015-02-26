<?php

class Template extends \Component {

	public static $component = array(
		'type' => 'component',
		'id' => 'template',
		'title' => 'Шаблон',
		'autoload' => true
	);

	// Render template
	// ---------------
	public function render() {}


	// Get list of components
	// ----------------------
	public static function getComponentsList() {

		// Read templates from components
		// ------------------------------
		$result = parent::getComponentsList();

		// Read templates from db
		// ----------------------
		$templateClass = \Core::getClass('template');
		$templates = $templateClass::find();

		if (!empty($templates)) {
			foreach($templates as $template) {
				$result[$template->id] = first_var($template->title, $template->id);
			}
		}

		// Return
		// ------
		return $result;
	}


	// Get template update time
	// ------------------------
	public function getUpdateTime() {

		// For external file
		// -----------------
		if (!empty($this->templateSourceFile)) {
			return @ filemtime(static::$component['componentPath'].'/'.$this->templateSourceFile);
		} else {
			$refClass = new ReflectionClass($this);
			return @ filemtime($refClass->getFileName());
		}

		return 0;
	}

	// Get template source
	// -------------------
	public function getSource() {

		// If source if provided
		// ---------------------
		if (!empty($this->templateSource)) return $this->templateSource;

		// If source file is provided
		// --------------------------
		if (!empty($this->templateSourceFile)) {

			$content = file_get_contents(static::$component['componentPath'].'/'.$this->templateSourceFile);
			if (!empty($content)) return $content;
		}

		// No any values
		// -------------
		return '';

	}


	// Load component directory override
	// ---------------------------------
	public static function registerComponent() {

		// If it's classBased, register as component
		// -----------------------------------------
		if (empty(static::$component['templateEngine']) || static::$component['templateEngine'] == 'classBased') {
			parent::registerComponent();
			return;
		}

		// Or append database
		// ------------------
		else {

			// Get source objects
			// ------------------
			$templateObject = new static();

			// Get template id
			// ---------------
			$templateID = static::$component['id'];

			// Try to load from database
			// -------------------------
			$templateClass = \Core::getClass('template');
			if (empty($templateClass)) return;

			// Try to load
			// -----------
			$templateInstance = $templateClass::findOne(array('query' => array('id' => $templateID)));

			// Create new template object
			// --------------------------
			if (empty($templateInstance)) {
				$templateInstance = $templateClass::getInstance();
			}
			
			// Get update time
			// ---------------
			$objectUpdateTime = $templateObject->getUpdateTime();
			$templateUpdateTime = $templateInstance->templateUpdateTime;

			// Skip if it's fresh
			// -----------------
			if ($objectUpdateTime <= $templateUpdateTime) {
				return;
			}

			// Set new properties
			// ------------------
			$templateInstance->set(array(
				'id' => $templateID,
				'engineType' => first_var(@ static::$component['templateEngine'], 'html'),
				'title' => first_var(@ static::$component['title'], $templateID),
				'description' => @ static::$component['description'],
				'source' => $templateObject->getSource(),
				'class' => @ static::$component['templateClass'],
				'templateUpdateTime' => time(),
                'assets' => @ static::$component['assets'],
                'headContentAddin' => @ static::$component['headContentAddin']
			));


			// Save
			// ----
			$templateInstance->save();
		}




	}


}
