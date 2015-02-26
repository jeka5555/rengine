<?php

namespace Search\Widgets;

class SiteSearch extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'id' => 'site-search',
		'title' => 'Виджет для поиска по сайту'
	);

	// Render
	// ------
	public function render() {
		$content = '
			<form action="/search" method="GET">
				<input type="text" placeholder="найти на сайте" name="query" class="search_input" value="">
				<button type="submit" class="btn btn_search"></button>
			</form>
    ';
		return $content;
	}
}