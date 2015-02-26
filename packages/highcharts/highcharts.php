<?php

namespace Modules;

include_once('.lib/Highchart.php');

class Highcharts extends \Module {

	// Компонент
	// ---------
	public static $component = array(
		'type' => 'module',
		'id' => 'highcharts',
		'settings' => array(
			'active' => true
		),
		'title' => 'Highcharts.Графики'
	);

}
