<?php

namespace Widgets;

class Block extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'type' => 'widget',
		'id' => 'block',
		'editable' => true,
		'title' => 'Блок'
	);


	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'id' => array('type' => 'text', 'title' => 'Идентификатор')
		);
	}

	// Generate final HTML class
	// -------------------------
	public function generateHtmlClasses() {

		// Get classes
		// -----------
		if (is_array(@ $this->options['htmlClasses'])) $htmlClasses = $this->options['htmlClasses'];
		else if (is_string(@ $this->options['htmlClasses'])) $htmlClasses = explode(" ", $this->options['htmlClasses']);
		else $htmlClasses = array();

		// Put some standart classes for blocks
		// ------------------------------------
		$htmlClasses[] = 'block';
		$htmlClasses[] = 'block-'.$this->args['id'];

		return $htmlClasses;
	}

	// Blocks have other ID's than widgets
	// -----------------------------------
	public function generateHtmlID() {
		if (!isset($this->htmlID)) $this->htmlID = first_var(@ $this->options['htmlID'], @ $this->args['id'], 'block'.rand(0,3000000));
		return $this->htmlID;
	}

	// Add controller script
	// ---------------------
	public function addControllerScript() {


		// Is user allowed to edit this?
		// -----------------------------
		$editorUser = \Rules::check(array(
			array('type' => 'or', 'rules' => array(
				array('type' => 'userRole', 'role' => 'administrator'),
				array('type' => 'userRole', 'role' => 'super'),
			))
		));

		// Check for edit mode
		// --------------------
		if (!$editorUser || \Core::getApplication()->data['editMode'] != true) return false;

		// Режим редактирования
		// -------------------------
		$editorUser = \Rules::check(array(
			array('type' => 'or', 'rules' => array(
				array('type' => 'userRole', 'role' => 'administrator'),
				array('type' => 'userRole', 'role' => 'contentManager'),
			))
		));

		if (!empty($this->_id) || ! $editorUser) return false;

		$htmlID = $this->generateHtmlID();

		// Code
		// ----
		$controllerArgs = array(
			'useWrapper' => true,
			'title' => first_var(@ $this->args['id']),
			'widget' => '#'.$htmlID,
			'id' => $this->args['id']
		);

		// Add script
		// ----------
		\Events::send('addEndScript', 'blockController'.str_replace($htmlID,'-','').' = new BlockController('.json_encode($controllerArgs).'); ');

	}

	// Render widget
	// -------------
	public function get() {

			$content = '';

			// If content is set
			// -----------------
			if (!empty($this->args['content'])) $content = $this->args['content'];

			// Get
			// ---
			else {

				// Take module
				// -----------
				$widgetsModule = \Core::getModule('widgets');

				// Widgets
				// --------
				$widgets = array();

				// Take only widgets of this block
				// -------------------------------
				if (!empty($widgetsModule->widgets)) {
					foreach ($widgetsModule->widgets as $widget) {

							// If block is correct, add to output
							// ----------------------------------
							if (@ $widget->options['block'] == @ $this->args['id']) {
								$widgets[] = $widget;
							}


					}
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
			}


            // Если блок пустой и для него установлен атрибут вывода 404-ой ошибки
            // ------------------------------------
            if (@$this->args['404IfEmpty'] == true and empty($content)) {
                header("HTTP/1.0 404 Not Found");
            }




        // Get tag block
			// -------------
			$tag = first_var(@$this->options['tag'], 'section');


			// Build output tag
			// ----------------
			if ($tag != false) {
				$content = \Content::buildTag(array(
					'htmlID' => $this->generateHtmlID(),
					'htmlClasses' => $this->generateHtmlClasses(),
					'widgetID' => @ $this->_id,
					'htmlAttributes' => @ $htmlAttributes,
					'tag' => 'section',
					'content' => $content
				));
			}

			// Add controller
			// --------------
			$this->addControllerScript();

            // Wrap final content
            // ------------------
            if (!empty($this->options['wrappers'])) {
                $content = $this->wrap($content);
            }



			// Return content
			// --------------
			return $content;

	}
}
