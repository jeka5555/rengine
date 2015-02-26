<?php

namespace Core\Modules;

class Menu extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'menu',
		'title' => 'Управление меню'
	);

	public $menu = array();

	// Add item to menu
	// ----------------
	public function addItemToMenu($menuID, $item) {
		if (empty($this->menu[$menuID])) $this->menu[$menuID] = array();
		$this->menu[$menuID][] = $item;
	}

	// Clear menu
	// ----------
	public function clearMenu($menuID = null) {
		$this->menu[$menuID] = array();
	}

}