<?php

namespace Highcharts\Packages;

class Highcharts extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'highcharts',
		'title' => 'Highcharts',
		'description' => 'Модуль позволяет строить графики на основе Highcharts',
		'icon' => '/packages/highcharts/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);
	
}	