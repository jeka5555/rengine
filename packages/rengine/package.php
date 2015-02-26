<?php

namespace REngine\Packages;

class REngine extends \Package {

	// Component registration
	// ----------------------
	public static $component = array(
		'id' => 'rengine',
		'title' => 'Тестовый сайт REngine',
		'description' => '',
		'icon' => '/packages/rengine/icon.png',

	);


    public static function installPackage() {

        $nodeClass = \Core::getClass('node');

        // Add site node
        $siteNode = new $nodeClass(array(
            "type" => "site",
        ));

        parent::installPackage();
    }

}	