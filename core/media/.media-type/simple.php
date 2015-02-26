<?php

namespace Core\Media\MediaTypes;

class Simple extends \Core\Media\Components\MediaType {

	// Component
	// ---------
	public static $component = array('type' => 'media-type', 'id' => 'simple', 'title' => 'Общий тип');

	// Render
	// ------
	public function renderMedia() {
		$title = first_var(@ $this->title, '...');
		return '<a href="'.$this->getURI().'">'.$title.'</a>';
	}

	// Post upload function
	// --------------------
	public function postUpload() {

	}

	// Get download URI
	// ----------------
	public function getURI() {
		
		if (!empty($this->sourceURI)) return $this->sourceURI;
		else {
			$url = '/media/'.$this->_id;
			if (!empty($this->fileExtension)) $url .= '.'.$this->fileExtension;
			return $url;
		}
	}

}
