<?php

namespace Core\DataViews;

class Content extends \Component {

	public static $component = array(
		'type' => 'dataView',
		'id' => 'content'
	);

	// Отображение
	// -----------
	public function execute($args = array()) {

		// Skip non arrays
		// ---------------
		if (!is_array($this->value)) {
			return;
		}

		// Content will be collected here
		// ------------------------------
		$content = '';

		// Render blocks
		// -------------
		foreach($this->value as $block) {

			// Skip for text mode
			// ------------------
			if (@ $this->options['textMode'] == true && $block['type'] != 'text') continue;

			// Add block to content
			// --------------------
			$content .= \Core::getModule('content')->actionRenderBlock($block);
		}

		// Escape
		// ------
		if (@ $this->options['escape'] == true && !empty($content)) {
			$content = htmlspecialchars($content);
		}

		// Strip tags
		// ----------
		if (@ $this->options['stripTags'] == true && !empty($content)) {
			$content = strip_tags($content);
		}

		// Crop
		// ----
		if (isset($this->options['maxChars'])) {
			$content = mb_strimwidth($content, 0, @ $this->options['maxChars'], '...');
		}

		// Return
		// ------
		return $content;
	}
}
