<?php

namespace Core\Admin\AdminApplications;

class MediaInsert extends \Core\Admin\Components\AdminApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'mediaInsert',
		'title' => 'Вставка файлов'
	);
	
	
	// Get media type
	// ----------------
	function commandGetMediaType($args = array()) {
	
		if (empty($args['mediaID'])) return null;
		
		$mediaClass = \Core::getClass('media');
		$media = $mediaClass::findPK($args['mediaID']);
		
		if (!empty($media->type))
			return $media->type;
		else
			return false;
	}

	// Get image
	// ----------------
	function commandGetContent($args = array()) {
	
		if (empty($args['mediaID'])) return null;   
		
		$content = \Widgets::get('media', @$args['data'], array('tag' => false));
		
		return $content;
	}

}
