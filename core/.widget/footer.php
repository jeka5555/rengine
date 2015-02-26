<?php

namespace Widgets;

class Footer extends \Widget {

	// Component
	// ---------
	static $component = array(
		'id' => 'footer',
		'group' => 'generic',
		'editable' => true,
		'tag' => 'footer',
		'title' => 'Универсальный подвал страницы',
		'hasSettings' => true
	);

	public function getWidgetArgsFormat() {
		return array(
			'companyName' => array('type' => 'text', 'title' => 'Компания чей сайт'),
            'text' => array('type' => 'text', 'title' => 'Текст копирайта')
		);
	}

	// Render function
	// ---------------
	public function render() {

		$content = '
		<div class="copyrights">
			&copy;'.date("Y").'.'.first_var(@$this->args['companyName'], @\Core::$settings['title']).'. '.first_var(@$this->args['text'], 'Все права защищены.').'
		</div>
		';

		return $content;
	}

}
