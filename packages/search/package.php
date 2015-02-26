<?php

namespace Search\Packages;

class Search extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'search',
		'title' => 'Поиск по сайту',
		'description' => 'Осуществляет операции для поиска по ресурсам сайта, как и по различным объектам. Предоставляет страницу для результатов поиска',
		'icon' => '/packages/search/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)			
	);

}