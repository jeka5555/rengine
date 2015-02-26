<?php
namespace RAN\Widgets;

class GeoSwitchRegion extends \Widget {

	// Component info
	// --------------
	public static $component = array('id' => 'geo-switch-region');

	// Render function
	// ---------------
	public function render() {

		// Get geo module
		// --------------
		$geoModule = \Core::getModule('geo');

		// Get region id
		// -------------
		$regionTitle = 'регион не определен';
		$regionID = $geoModule->regionID;
		if (!empty($regionID)) {
			$regionClass = \Core::getComponent('class', 'geo-region');
			$region = $regionClass::findPK($regionID);
			if (!empty($region)) {
				$regionTitle = $region->title;
			}
		}

		// Render
		// ------

		$content = '<div class="region-title"><a href="#">'.$regionTitle.'</a><span class="select"></a></div>';

		return $content;
	}

}