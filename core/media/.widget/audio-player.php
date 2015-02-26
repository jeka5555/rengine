<?php

namespace Core\Media\Widgets;

class AudioPlayer extends \Widget {

	// Component
	// ---------
	public static $component = array('type' => 'widget', 'id' => 'audio-player');

	// Render function
	// ---------------
	public function render() {

		// Get file or playlist
		// --------------------
		if(!empty($this->args['file'])) $file = '&amp;file='.$this->args['file']; else $file = ''; // Файл или поток
		if(!empty($this->args['playList'])) $playList = '&amp;pl='.$this->args['playList']; else $playList = ''; // Плейлист

		// Set options
		// -----------
		$skin = first_var(@ $this->args['skin'], '/modules/core/assets/player/skin.audio/minimal');
		$autoplay = first_var(@ $this->args['autoplay'], 0);

		// Output player
		// -------------
		$content = '
			<object type="application/x-shockwave-flash" data="/modules/core/assets/player/uppod.swf" width="400" height="33">
			<param name="wmode" value="transparent" />
			<param name="allowScriptAccess" value="always" />
			<param name="movie" value="/modules/core/assets/player/uppod.swf" />
			<param name="flashvars" value="st='.@$skin.@$file.@$playList.'&amp;auto='.@$autoplay.'" />
		</object>';

		return $content;

	}

}
