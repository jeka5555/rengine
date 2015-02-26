<?php

namespace TemplateEngines;

// Движок для twig-файлов
// ----------------------
class Twig extends \TemplateEngine {

	// Информация о компоненте
	// -----------------------
	public static $component = array(
		'type' => 'templateEngine',
		'id' => 'twig'
	);

	// Это система twig
	// ----------------
	public static $env = null;

	// Код необходимый для инициализации системы
	// -----------------------------------------
	public static function initComponent() {

		// Init component
		// --------------
		parent::initComponent();

		// Create a loader
		// ---------------
		$loader = new \TwigLoader();

		// Create new environment
		// ----------------------
		static::$env = new \Twig_Environment($loader,
			array('autoescape' => false, 'debug' => true)
		);

		// Block render function
		// ---------------------
		$blockFunction = new \Twig_SimpleFunction( 'render_block',
			function ($blockID = null, $args = array(), $options = array()) {
				return \Blocks::get($blockID, $args, $options);
			}
		);
		static::$env->addFunction($blockFunction);

		// Template render function
		// ------------------------
		$templateFunction = new \Twig_SimpleFunction( 'render_template',
			function ($templateID = null, $args = array(), $options = array()) {
				return \Templates::get($templateID, $args, $options);
			}
		);
		static::$env->addFunction($templateFunction);


		// Widget render function
		// ----------------------
		$widgetFunction = new \Twig_SimpleFunction( 'render_widget',
			function ($widgetID = null, $args = array(), $options = array()) {
				return \Widgets::get($widgetID, $args, $options);
			}
		);
		static::$env->addFunction($widgetFunction);

		// View render function
		// --------------------
		$viewFunction = new \Twig_SimpleFunction( 'render_view',
			function ($id = null, $value, $options = array()) {
				return \DataView::get($id, $value, $options);
			}
		);
		static::$env->addFunction($viewFunction);

	}

	// Display
	// -------
	public static function render($templateObject, $data) {

		// Render
		// ------
		return static::$env->render($templateObject['source'], $data);

	}
}
