<?php

namespace Twig\Packages;

class Twig extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'twig-templates',
		'title' => 'Шаблонизатор Twig',
		'description' => 'Модуль позволяет использовать шаблоны в формате Twig',
		'icon' => '/packages/twig-templates/icon.png',
		'deps' => array(
			array('id' => 'core', 'version' => 5)
		)
	);
	

}	