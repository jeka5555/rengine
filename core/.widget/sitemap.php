<?php

namespace RAN;

class Sitemap extends \Widget {

	// Init component
	// --------------
	static $component = array(
		'type' => 'widget',
		'id' => 'sitemap',
		'title' => 'Карта сайта',
		'group' => 'ran',
		'editable' => true,
	);

	// Render
	// ------
	public function render() {
		return 'карта сайта не раализована';
	}

}