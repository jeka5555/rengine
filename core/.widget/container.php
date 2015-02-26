<?php

namespace Widgets;

class Container extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'type' => 'widget',
		'id' => 'container',
		'editable' => true,
		'title' => 'Контейнер'
	);


	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'widgets' => array('type' => 'list', 'title' => 'Виджеты', 'format' => array('type' => 'object', 'class' => 'widget'))
		);
	}

	// Generate final HTML class
	// -------------------------
	public function generateHtmlClasses() {

		$htmlClasses = parent::generateHtmlClasses();
		$htmlClasses[] = 'container';
		return $htmlClasses;
	}

	// Blocks have other ID's than widgets
	// -----------------------------------
	public function generateHtmlID() {
		if (!isset($this->htmlID)) $this->htmlID = first_var(@ $this->options['htmlID'], @ $this->args['id'], 'container'.rand(0,3000000));
		return $this->htmlID;
	}

	// Render widget
	// -------------
	public function get() {


		// Don't show empty content
		// ------------------------
		if (empty($this->args['widgets'])) return;

		$content = '';

		// Take module
		// -----------
		$widgetClass = \Core::getClass('widget');
		$widgetsModule = \Core::getModule('widgets');


		$widgets = array();
		foreach($this->args['widgets'] as $widgetID) {

			// Load widgets
			// ------------
			$widget = $widgetClass::findPK($widgetID);
			if (empty($widget)) continue;

			// Add to widget's list
			// --------------------
			$widgets[] = $widgetsModule->getWidget($widget);
		}

		// Render widgets
		// --------------
		if (!empty($widgets)) {

			// Sort
			// ----
			usort($widgets, function($a, $b) {

				// Zero
				// ----
				if (empty($a->options['order'])) $a->options['order'] = 0;
				if (empty($b->options['order'])) $b->options['order'] = 0;

				// Else
				// ----
				if (@ (float) $a->options['order'] < @ (float) $b->options['order']) return -1;
				if (@ (float) $a->options['order'] > @ (float) $b->options['order']) return 1;

				// They are equal
				// --------------
				return 0;
			});

			// Render widgets
			// --------------
			foreach($widgets as $widget) {
				$content .= $widget->get();
			}
		}


		// Build output tag
		// ----------------
		$content = \Content::buildTag(array(
			'htmlID' => $this->generateHtmlID(),
			'htmlClasses' => $this->generateHtmlClasses(),
			'widgetID' => @ $this->_id,
			'htmlAttributes' => @ $htmlAttributes,
			'content' => $content,
			'tag' => first_var(@ $this->options['htmlTag'], 'div')
		));


		// Add controller
		// --------------
		$this->addControllerScript();

		// Return content
		// --------------
		return $content;

	}
}
