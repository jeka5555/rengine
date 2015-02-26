<?php

namespace Gazbank\Popups;

class ConfirmCity extends \Core\Components\Popup {

	// Component registration
	// ----------------------
	public static $component = array('id' => 'confirm-city');

	// Data
	// ----
	public $title = 'Подтверждение текущего города';
	public $showTitle = false;
	public $buttons = null;
	public $htmlClass = 'confirm-city';
	public $width = 300;
	public $controller = 'GeoConfirmCityController';


	// Get data
	// --------
	public function getData() {
		return array(
			'cityID' => 'samara'
		);
	}


	// Get content
	// -----------
	public function getContent() {

		$content = '<div class="confirm-city">
			<h2>Ваш город Самара?</h2>
			<div class="options">
				<span class="option yes">да</span>
				<span class="option no">нет</span>
			</div>
		</div>';

		return $content;
	}
}
