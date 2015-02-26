<?php

namespace Shop\Packages;

class Shop extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'shop',
		'title' => 'Интернет магазин Rengine',
		'description' => 'Модуль содержит функционал для построение интернет-магазина',
		'icon' => '/packages/reshop/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5.5)
		)
	);

}