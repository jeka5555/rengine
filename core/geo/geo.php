<?php

namespace Core\Modules;

include_once('.lib/geo.php');

class Geo extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'geo',
		'title' => 'География и позиционирование',
		'hasSettings' => true
	);

	// Component settings format
	// -------------------------
	public static $componentSettingsFormat = array(
		'defaultCity' => array('type' => 'object', 'title' => 'Город по-умолчанию', 'class' => 'geo-city'),
		'confirmCity' => array('type' => 'boolean', 'title' => 'Запрашивать подтверждение города')
	);

	// Settings
	// --------
	public static $settings = array(
		'defaultCity' => '5279f815f9cd0e500f8d6404'
	);

	// Init module
	// -----------
	public function init() {

		$this->city = first_var(@ $_SESSION['geo-city'], static::$settings['defaultCity']);


		// Get current city
		// ----------------
		if (empty($this->city)) {

			// Get request
			// -----------
			$geoIP = new \Geo(array('charset' => 'utf-8'));
			$this->ip = $geoIP->ip;
			$this->city = $geoIP->get_value('city');
		}

		// Store city
		// ----------
		$_SESSION['geo-city'] = $this->city;

	}

	// Set city
	// --------
	public function actionSetCity($args) {
		$_SESSION['geo-city'] = $args['cityID'];
		\Events::send('reload');
	}

	// Get list of regions
	// ------------------
	public function actionGetRegionsList() {
		$regionClass = \Core::getComponent('class', 'geo-region');
		$regions = $regionClass::find();
		$result = array();
		foreach($regions as $region) {
			$result[] = array(
				'title' => $region->title,
				'id' => $region->_id,
				'type' => $region->type
			);
		}
		return $result;
	}

}
