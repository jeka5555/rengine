<?php

namespace Core\Media\Widgets;

class VideoPlayer extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'type' => 'widget',
		'id' => 'videoPlayer',
		'editable' => true,
		'group' => 'media',
		'title' => 'Видео-плеер'
	);

	// Render function
	// ---------------
	public function render() {

		// Get file
		// --------
		$file = @ $this->args['file'];
		if (empty($file)) return '';

		// Settings
		// --------
		$skin = first_var(@ $this->args['skin'], '/core/.assets/player/skin.video/default');
		$width = first_var(@ $this->args['width'], 545);
		$height = first_var(@ $this->args['height'], 365);
		$autoplay = first_var(@ $this->args['autoplay'], 0);

		// Player code
		// -----------
		$content = '<object width="'.$width.'" height="'.$height.'">
			<param name="allowFullScreen" value="true" />
			<param name="allowScriptAccess" value="always" />
			<param name="wmode" value="transparent" />
			<param name="movie" value="/core/.assets/player/uppod.swf" />
			<param name="flashvars" value="st='.$skin.'&amp;file='.$file.'&amp;autoplay='.$autoplay.'" />
			<embed src="/core/.assets/player/uppod.swf"
				type="application/x-shockwave-flash"
				allowscriptaccess="always"
				allowfullscreen="true"
				wmode="transparent"
				flashvars="st='.$skin.'&amp;file='.$file.'" width="'.$width.'" height="'.$height.'&amp;autoplay='.$autoplay.'">
			</embed>
		</object>';

		return $content;

	}
}
