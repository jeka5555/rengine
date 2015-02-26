<?php
namespace Core\Modules;

class SEO extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'seo',
		'title' => 'SEO.Поисковая оптимизация',
		'hasSettings' => true
	);

	// Component settings
	// ------------------
	public static $componentSettingsFormat = array(
		'keywordsMode' => array('type' => 'select', 'title' => 'Режим добавления ключевых слов по-умолчанию', 'values' => array('append' => 'Дополнять', 'replace' => 'Замещать')),
		'allowMixDescriptions' => array('type' => 'boolean', 'title' => 'Разрашеить склеивать описания'),
		'enableNodesDescription' => array('type' => 'boolean', 'title' => 'Использовать описания в узлах'),
		'enableResourcesDescription' => array('type' => 'boolean', 'title' => 'Использовать описания в ресурсах'),
	);

	// Settings
	// --------
	public static $settings = array(
		'keywordsMode' => 'append',
		'allowMixDescriptions' => false,
		'enableNodesDescription' => true,
		'enableResourcesDescription' => true
	);

	public $keywords = array(); // Keywords are here
	public $description = null; // Page description


	// Append keywords
	// ---------------
	public function setKeywords($keywords, $mode = 'append') {

		switch ($mode) {

			// Replace keywords
			// ----------------
			case 'replace':
				$this->keywords = $keywords;
				break;

			// Default way - append
			// --------------------
			default:
				$this->keywords += $keywords;
				break;
		}
	}

	// Clear all keywords
	// ------------------
	public function clearKeywords() {
		$this->keywords = array();
	}

	// Set page description
	// --------------------
	public function setDescription($description) {
		$this->description = $description;
	}


}