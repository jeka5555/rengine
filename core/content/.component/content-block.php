<?php

namespace Core\Components;

class ContentBlock extends \Component {

	// Component
	// ---------
	public static $component = array(
		'type' => 'component',
		'id' => 'content-block',
		'autoload' => true,
		'title' => 'Блок визуального редактора'
	);

	// Render whole block
	// ------------------
	public function render() {

		// Get content
		// -----------
		$content = $this->renderContent();

		// Render with wrappers
		// --------------------
		$content = \Content::buildTag(array(
			'tag' => first_var($this->tag, 'div'),
			'content' => $content,
			'css' => @ $this->css,
			'htmlID' => @ $this->htmlID,
			'htmlClasses' => @ $this->htmlClasses,
			'htmlAttributes' => @ $this->htmlAttributes,
		));

		return $content;
	}

	// Render block content
	// --------------------
	public function renderContent() {
		return '';
	}


}
