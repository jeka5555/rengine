<?php

namespace REngine\Templates;

class PageMain extends \Template {

	// Component
	// ---------
	public static $component = array(
		'id' => 'page-main',
		'title' => 'REngine. Шаблон главной страницы',
		'templateClass' => 'page',
		'templateEngine' => 'twig',
        'assets' => array('css' => array('http://fonts.googleapis.com/css?family=Galdeano&subset=latin,cyrillic')),
        'headContentAddin' => '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0" />'
	);       


	// Template source
	// ---------------           
	public function getSource() {
  
    return '	
    {{ render_block("header") }}
    {{ render_block("menu") }}
    {{ render_block("content") }}
    {{ render_block("footer") }}
    ';
  }

}