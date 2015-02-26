<?php

namespace Shop\Popups;


class Order extends \Core\Components\Popup {

	// Component registration
	// ----------------------
	public static $component = array('id' => 'shop-order');

	// Data
	// ----
	public $title = 'Корзина';
	public $class = 'dialog';
	public $modal = true;
	public $buttons = null;
	public $minimizable = false;
	public $collapsable = false;
	public $resizable = false;

	// Get content
	// -----------
	public function getContent() {
		$content = \Core::getModule('widgets')->createWidget(array('shop-cart-list'))->get();
		return $content;
	}
}
