<?php

namespace Shop\Widgets;


class Cart extends \Widget {


	// Component
	// ---------
	public static $component = array(
		'id' => 'cart',
		'title' => 'Корзина',
		'editable' => true
	);

	// Render
	// ------
	public function render() {

		$count = 0;
		$total = 0;

		// Node class
		// ----------
		$nodeClass = \Core::getClass('node');

		// Get count and total price
		// -------------------------
		if (!empty($_SESSION['shop-cart'])) {
			foreach($_SESSION['shop-cart'] as $productID => $productCount) {
				$count += $productCount;

				$productNode = $nodeClass::findPK($productID);
				if (!empty($productNode)) {
					$total += first_var($productNode->data['price'], 0) * $productCount;
				}
			}
		}

		$content = '   
			<div class="shop-cart-title">Ваш заказ</div>
			<div class="separation-line"></div>
			<div class="shop-cart-data">
				<div><span class="shop-cart-data-value count">'.$count.'</span> товаров</div>
				<div><span class="shop-cart-data-value total">'.\DataView::get('number', $total, array('divider' => ' ')).'</span> рублей</div>			
			</div>
			<a class="button red-button" href="/shop/cart"><span class="title">оформить</span></a>
			 
		';

		// Return content
		// --------------
		return $content;
	}
}
