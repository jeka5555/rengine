<?php

namespace Core\Media\Objects;

class Vimeo extends \Core\Media\MediaTypes\Simple {

	// Component
	// ---------
	public static $component = array( 'type' => 'media-type', 'id' => 'vimeo', 'title' => 'Видео из Vimeo');

	// Render function
	// ---------------
	public function renderMedia() {

		// Init size
		// ---------
		$width = first_var(@ $this->width, 400);
		$height = first_var(@ $this->height, 300);

		$content = '<iframe src="http://player.vimeo.com/video/'.$this->id.'?title=0&amp;byline=0&amp;portrait=0" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		return $content;
	}
}
