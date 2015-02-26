<?php

namespace Shop\Nodes;

class Shop extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'shop',
		'title' => 'Интернет-магазин'
	);

	// product node format
	// -----------------------
	public static function getNodeDataFormat() {
		return array(
			'categoryPreviewMode' => array('type' => 'text', 'title' => 'Категория. Режим превью', 'value' => 'preview'),
			'categoryFullMode' => array('type' => 'text', 'title' => 'Категория. Полный режим', 'value' => 'full'),
			'productPreviewMode' => array('type' => 'text', 'title' => 'Продукт. Режим превью', 'value' => 'preview'),
			'productFullMode' => array('type' => 'text', 'title' => 'Продукт. Полный режим', 'value' => 'full')
		);
	}

	// Node data structure
	// -------------------
	public static function getNodeDataStructure() {
		return array(
			array('type' => 'block', 'title' => 'Настройки вывода элемента', 'elements' => array(
				array('type' => 'form', 'properties' => array('categoryPreviewMode', 'categoryFullMode'))
			))
		);
	}



	// Show shopping cart
	// ------------------
	public function showCart() {

		// Get widget
		// ----------
		$cartWidget = @ $this->roleWidgets['cart'];
		if (empty($cartWidget)) return;

		// Render widget
		// -------------
		$cartWidget->out();

	}

	// Show shopping order
	// ------------------
	public function showOrder() {

		// Get widget
		// ----------
		$orderWidget = @ $this->roleWidgets['order'];
		if (empty($orderWidget)) return;

		// Render widget
		// -------------
		$orderWidget->out();

	}

	// Show menu
	// ---------
	public function showMenu() {

		// Get widget
		// ----------
		$menuWidget = @ $this->roleWidgets['categories-menu'];
		if (empty($menuWidget)) return;

		// Render widget
		// -------------
		$menuWidget->args['root'] = $this->_id;
		$menuWidget->args['ignoreMenuID'] = true;
		$menuWidget->args['ignoreMenuVisibility'] = true;
		$menuWidget->args['maxDepth'] = 3;
		$menuWidget->args['types'] = array('shop-category');
		$menuWidget->options['htmlClasses'][] = 'shop-categories-menu';

		// Out
		// ---
		$menuWidget->out();

	}

	// Get shop working
	// ------------------
	public function passNode() {

		// Go forward
		// ----------
		parent::passNode();

		// Show items
		// ----------
		$this->showCart();
		$this->showMenu();

	}

	// Show cart list
	// --------------
	public function showCartList() {

		$cartWidget = \Core::getModule('widgets')->createWidget(array('shop-cart-list', null, array('block' => 'content')));
		$cartWidget->out();

	}

	// Process path
	// ------------
	public function processPath($path, $pathIndex = 0) {

		$action = @ $path[$pathIndex + 1];

		switch ($action) {

			// Shopping cart
			// -------------
			case 'cart':
				$this->passNode();
				$this->showCartList();
				break;

			// Shopping order
			// -------------
			case 'order':
				$this->passNode();
				$this->showOrder();
				break;  


			// Default
			// -------
			default:        
				parent::processPath($path, $pathIndex);
				break;
		}

	}

	// Render shop
	// -----------
	public function renderModeFull() {

		$content = '<h1>'.\DataView::get('text', @ $this->title).'</h1>';
         

		// Configure
		// ---------
		$categoriesListWidget = \Core::getModule('widgets')->createWidget(array('nodes-list', array(), array('htmlClasses' => 'white-container')));
		$categoriesListWidget->args['type'] = 'shop-category';
		$categoriesListWidget->args['parent'] = $this->_id;
		$categoriesListWidget->args['mode'] = first_var(@ $this->display['categoryPreviewMode'], 'preview');     

		// Render widget
		// -------------
		$content .= $categoriesListWidget->get();

		// Get widget
		// ----------
		$productsListWidget = \Core::getModule('widgets')->createWidget(array('nodes-list', array(), array('htmlClasses' => 'white-container')));

		// Configure
		// ---------
		$productsListWidget->args['type'] = 'product';
		$productsListWidget->args['parent'] = $this->_id;
		$productsListWidget->args['mode'] = first_var(@ $this->display['categoryPreviewMode'], 'preview'); 

		// Render widget
		// -------------
		$content .= $productsListWidget->get();

		return $content;

	}

}
