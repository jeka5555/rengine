<?php

namespace Core\Media\MediaTypes;

class Audio extends \Core\Media\MediaTypes\Simple {

	// Component
	// ---------
	public static $component = array(
		'type' => 'media-type',
		'id' => 'audio',
		'title' => 'Звуковой файл'
	);

	// Render function
	// ---------------
	public function renderMedia() {
	   	$content = \Widgets::get('audioPlayer', array('file' => '/media/'.urlencode($this->_id)));
		return $content;
	}
}
