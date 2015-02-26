<?php

namespace Core\Media\MediaTypes;

class Flash extends \Core\Media\MediaTypes\Simple {

	// Component
	// ---------
	public static $component = array('type' => 'media-type', 'id' => 'flash', 'title' => 'FLASH-анимация');

	// Render function
	// ---------------
	public function renderMedia() {

		// Limit size
		// ----------
		$width = first_var(@ $this->width, 400);
		$height = first_var(@ $this->height, 300);

		$content = '
			<object width="'.$width.'" height="'.$height.'">
			<param name="movie" value="/media/'.$this->_id.'.'.$this->fileExtension.'">
			<embed src="/media/'.$this->_id.'.'.$this->fileExtension.'" width="'.$width.'" height="'.$height.'">
			</embed>
			</object>
		';

		return $content;
	}
}
