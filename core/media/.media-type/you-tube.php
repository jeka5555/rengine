<?php

namespace Core\Media\MediaTypes;

class YouTube extends \Core\Media\MediaTypes\Simple {

	// Component
	// ---------
	public static $component = array('type' => 'media-type', 'id' => 'youtube', 'title' => 'Видео из YouTube');

	// Render
	// ------
	public function renderMedia() {

		$width = first_var(@ $this->width, 400);
		$height = first_var(@ $this->height, 300);

		$content = '<iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$this->id.'" frameborder="0" allowfullscreen></iframe>';
		return $content;
	}
}
