<?php

namespace Banners\Packages;

class Banners extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'banners',
		'title' => 'Баннеры',
		'description' => 'Размещение баннеров на сайте. Управление, группировка, фильтры баннеров. Возможен подсчет просмотров',
		'icon' => '/packages/banners/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);
	

}	