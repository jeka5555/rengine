<?php

namespace Modules {

	// Модуль управления виджетами
	// ---------------------------
	class Widgets extends \Module {

		// Component
		// ---------------------
		static $component = array('type' => 'module', 'id' => 'widgets', 'title' => 'Виджеты');

		public $widgets = array(); // Widgets storage

		// Init module
		// -----------
		public static function initComponent() {

			// Parent init
			// -----------
			parent::initComponent();
			\Events::addListener('addWidget', \Core::getModule('widgets'));
		}

		// Event
		// -----
		public function eventAddWidget($event, $data = null) {
			$this->addWidget($data);
		}


		// Add widget
		// ----------
		public function addWidget($data = null) {

			// Create widget
			// -------------
			$widgetInstance = $this->createWidget($data);

			// Add widget
			// ----------
			if (!empty($widgetInstance)) {
				$this->widgets[] = $widgetInstance;
			}
		}


		// Create widget
		// -------------
		public function getWidget($widget) {

			// Get widget
			// ----------
			if (empty($widget)) return;
			$widgetType = \Core::getComponent('widget', $widget->type);
			if (empty($widgetType)) return;

			// Create widget
			// -------------
			$widget = $widgetType::getInstance(array(
				'id' => @ $widget->_id,
				'_id' => @ $widget->_id,
				'args' => @ $widget->args,
				'options' => @ $widget->options,
			));

			// Return widget
			// -------------
			return $widget;
		}


		// Return
		// ------
		public function actionGet($widget) {
			$widget = $this->createWidget($widget);
			return $widget->get();
		}

		// Create widget
		// -------------
		public function createWidget($data) {

			// Get class
			// ---------
			$widgetClass = \Core::getComponent('widget', $data[0]);
			if (empty($widgetClass)) return;

			// Widget
			// ------
			$widget = $widgetClass::getInstance(array('args' => @ $data[1], 'options' => @ $data[2]));
			return $widget;

		}


		// Get widget content
		// ------------------
		public function get($widgetID, $args = array(), $options = array()) {

			// Create widget
			// -------------
			$widget = $this->createWidget(array($widgetID, $args, $options));

			// Return widget
			// -------------
			if (!empty($widget)) {
				return $widget->get();
			}

		}



		// Move widget to new block
		// ------------------------
		public function actionMoveWidgetToBlock($args = array()) {

			// Guard
			// -----
			if (empty($args['widgetID']) || empty($args['block'])) return;

			// Get widget
			// ----------
			$widgetClass = \Core::getClass('widget');
			$widget = $widgetClass::findPK($args['widgetID']);
			if (empty($widget)) return;

			// Set new block
			//  ------------
			$widget->options['block'] = $args['block'];
			$widget->save();

			return true;
		}

		// Refresh widget content
		// ----------------------
		public function actionRefreshWidget($args = array()) {

			// Get widget class
			// ----------------
			$widgetClass = \Core::getClass('widget');

			// Get widget
			// ----------
			$widget = $widgetClass::findPK($args['id']);
			if (empty($widget)) return;

			// Return content
			// --------------
			return array(
				'content' => $widget->get()
			);
		}


		// Get widget args format
		// ----------------------
		public function actionGetWidgetArgsFormat($type = null) {

			// Get widget type
			// ---------------
			$widgetType = \Core::getComponent('widget', $type);
			if (empty($widgetType)) return;

			$widget = $widgetType::getInstance();
			$format = $widget->getWidgetArgsFormat();
			return $format;

		}
	}



}

namespace {

	class Widgets {

			// Wrap widgets module
			// -------------------
			public static function get($widgetID, $args = array(), $options = array()) {
				$widgetsModule = \Core::getModule('widgets');
				return $widgetsModule->get($widgetID, $args, $options);
			}
	}
}


