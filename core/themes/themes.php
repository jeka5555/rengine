<?php

namespace Core\Modules;

class Themes extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'themes',
		'title' => 'Темы оформления'
	);

	// Settings
	// --------
	public static $settings = array(
		'defaultTheme' => 'rework',
		'defaultAdminTheme' => 'rework'
	);

	// Actions
	// -------
	public $actions = array(
		'getThemesList' => array('method' => 'getThemesList'),
		'getThemeTemplates' => array('method' => 'getThemeTemplates')
	);

	// Themes
	// ------
	public $theme = null;
	public $adminTheme = null;

	// Set theme for page
	// ------------------
	public function setTheme($theme) {

	}

	// Set theme for admin
	// -------------------
	public function setAdminTheme($theme) {

	}

	// Get list of installed themes
	// ----------------------------
	public function getThemesList() {

		$result = array();

		// Считываем папки и перебирая их, извлекаем информацию о темах
		// ------------------------------------------------------------
		$dirs = glob(__DR__."themes/*", GLOB_ONLYDIR);
		if (!empty($dirs))
		foreach ($dirs as $dir) {
			$themeName = pathinfo($dir, PATHINFO_BASENAME);
			$themeData = @ yaml_parse_file($dir.'/theme.info');

			if (!empty($themeData['title'])) $title = $themeData['title'];
			else $title = $themeName;

			$result[$themeName] = $title;
		}

		return $result;

	}

	// Load theme
	// ----------
	public function loadTheme($themeID) {

        \Core::getApplication()->data['theme'] = $themeID;
        \Core::getApplication()->data['themePath'] = 'themes/'.$themeID;

		\Loader::importPackage(array('path' => \Core::getApplication()->data['themePath']));
		\Core::getModule('compiler')->compile();	
		\Extension::initComponents();
	}

	// Get theme themplates
	// --------------------
	public function getThemeTemplates($themeID = null) {

		\Loader::importObject(array('path' => 'themes/'.$themeID, 'as' => 'module', 'id' => $themeID));
		$result = array();
		foreach(\Extension::$ext['template'] as $tpl) {
			$result[$tpl['id']] = first_var(@$tpl['title'], $tpl['id']);
		}
		return $result;
	}

	// Get theme widgets
	// -----------------
	public function getThemeWidgets($themeID = null) {
	}

}
