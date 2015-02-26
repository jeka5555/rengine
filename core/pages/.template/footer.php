<?php

namespace Core\Pages\Templates;

class Footer extends \Template {

	public static $component = array(
		'id' => 'footer',
		'title' => 'Стандартый подвал сайта',
		'templateEngine' => 'twig'
	);

	// Template file
	// -------------
	public $templateSourceFile = 'footer.twig';

}