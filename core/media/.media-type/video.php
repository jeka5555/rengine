<?php

namespace Core\Media\MediaTypes;

class Video extends \Core\Media\MediaTypes\Simple {

	// Component
	// ---------
	public static $component = array('type' => 'media-type', 'id' => 'video', 'title' => 'Видео');

	// Render function
	// ---------------
	public function renderMedia() {
		return \Widgets::get('videoPlayer', array('file' => '/media/'.urlencode($this->_id)));
	}
}
