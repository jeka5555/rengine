<?php
namespace Core\ContentBlocks;

class HTML extends \Core\Components\ContentBlock {

	// Component
	// ---------
	public static $component = array(
		'id' => 'html',
		'type' => 'content-block',
		'title' => 'Отображение фрагмента HTML кода'
	);


	// Render
	// ------
	public function renderContent() {
		return @ $this->data;
	}
}