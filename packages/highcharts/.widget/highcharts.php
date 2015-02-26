<?php

namespace Core\Highchart\Widgets;

class Highcharts extends \Widget {

	// Component
	// ------------------------
	static $component = array(
		'type' => 'widget',
		'id' => 'highcharts',
		'editable' => true,
		'title' => 'Highcharts - построения графиков'
	);

	// Render function
	// ---------------
	public function render() {

        \Loader::$files['js'][] = 'http://code.highcharts.com/highcharts.js';

		// Create new highart instance
		// ---------------------------
		$chart = new \Highchart();

		// Set options
		// -----------
		foreach($this->args as $key => $option) {
			$chart->$key = $option;
		}

		// If no any target, render locally
		// --------------------------------
		if(!isset($this->args['chart']['renderTo'])) {
			$chartID = 'highchart_'.uniqid();
    	    $content = '<div id="'.$chartID.'"></div>';
		}

		// Else, just set target
		// ---------------------
		else {
			$chartID = $this->args['chart']['renderTo'];
		}

		// Set widget
		// ----------
		$chart->chart->renderTo = $chartID;


		// Submit script
		// -------------
        \Events::send('addEndScript', '
        Highcharts.setOptions({
            lang: {
                shortMonths: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
                months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
                weekdays: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
                shortMonths: ["Янв", "Фев", "Март", "Апр", "Май", "Июнь", "Июль", "Авг", "Сент", "Окт", "Нояб", "Дек"],
                exportButtonTitle: "Экспорт",
                printButtonTitle: "Печать",
                rangeSelectorFrom: "С",
                rangeSelectorTo: "По",
                rangeSelectorZoom: "Период",
                downloadPNG: "Скачать PNG",
                downloadJPEG: "Скачать JPEG",
                downloadPDF: "Скачать PDF",
                downloadSVG: "Скачать SVG",
                printChart: "Напечатать график",
                resetZoom: "Сбросить увеличение"
            }
        });
        ');
		\Events::send('addEndScript', $chart->render($chartID));

		return @$content;
	}
}
