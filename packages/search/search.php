<?php

namespace Modules;

class Search extends \Module {

	public static $component = array('id' => 'search', 'title' => 'Поиск по сайту');

	// Execute site search
	// -------------------
	public function actionSearch($data) {
		\Core::getApplication()->data['location'] = '/search?query='. htmlspecialchars($data);
	}
}
