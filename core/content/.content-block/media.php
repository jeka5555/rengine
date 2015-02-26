<?php
namespace Core\ContentBlocks;

class Media extends \Core\Components\ContentBlock {

	// Component
	// ---------
	public static $component = array(
		'id' => 'media',
		'title' => 'Вывод медиа-файлов'
	);

	// Render
	// ------
	public function renderContent() {
		return \DataView::get('media', $this->data['mediaID']);
	}
}