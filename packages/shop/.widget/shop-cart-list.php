<?php

namespace Shop\Widgets;

class ShopCartList extends \Widget {

	public static $component = array(
		'id' => 'shop-cart-list',
		'title' => 'Список товаров'
	);


	public function render() {

		// Title
		// -----
		$content = '';

		// Cart is empty
		// -------------
		if (empty($_SESSION['shop-cart'])) {
			$content .= '<div>корзина пуста</div>';
			return $content;
		}

		// Add id
		// ------
		$widgetID = 'shopCart'.uniqid();
		$this->options['htmlClasses'][] = $widgetID;

		// Add
		// ---
		$itemsContent = '';
		foreach($_SESSION['shop-cart'] as $itemID => $itemCount) {
			$itemWidget = \Core::getModule('widgets')->createWidget(array('node', array(
				'id' => $itemID,
				'mode' => 'cart'
			)));
			$itemsContent .= $itemWidget->get();
		}

		// Get count and total price
		// -------------------------
		$total = 0;
		$count = 0;
		if (!empty($_SESSION['shop-cart'])) {

			$nodeClass = \Core::getClass('node');
			foreach($_SESSION['shop-cart'] as $productID => $productCount) {
      	$count += $productCount;

				$productNode = $nodeClass::findPK($productID);
				if (!empty($productNode)) {
					$total += first_var($productNode->data['price'], 0) * $productCount;
				}
			}
		}
		
		$orderInfo = '
			<div class="order-total">				
				<div class="count"><span class="label">товаров</span> <span class="value">'.$count.'</span></div>
				<div class="total"><span class="label">общая сумма:</span> <span class="value">'.$total.'</span> <span class="label">рублей</span></div>
				<div class="buttons">     
					<button class="button red-button clear-cart has-icon"><span class="icon"></span><span class="title">очистить</span></button>
					<button class="button green-button order-button has-icon"><span class="icon"></span><span class="title">оформить заказ</span></button>
				</div>
			</div>
		';

		// Append controls
		// ---------------
		$content = '
		<h1>Ваш заказ</h1>
		'.$orderInfo.'
		<div class="items">'.@$itemsContent.'</div>
		'.$orderInfo.'
		';


		// Link to script
		// --------------
		$args = array('widget' => '.'.$widgetID);
		\Events::send('addEndScript', '
			new CartList('.json_encode($args).');               
		');

		// Return
		// ------
		return $content;
	}
}
