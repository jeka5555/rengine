<?php

namespace Shop\Modules;


class Shop extends \Module {

	// Component
	// ---------
	public static $component = array(
		'id' => 'shop',
		'title' => 'Интернет магазин',
		'hasSettings' => true
	);
	
	public static $settings = array(
		'currency' => array('руб.', '$'),
		'units' => array('шт.', 'кг.'),
	);

	public static $componentSettingsFormat = array(
		'currency' => array('type' => 'list', 'title' => 'Валюты магазина', 'format' => array(
			'type' => 'text', 'title' => 'Название'
		)),
		'units' => array('type' => 'list', 'title' => 'Единицы измерения', 'format' => array(
			'type' => 'text', 'title' => 'Название'
		))
	);

    // Градация цен по статусам
    public static $statusPrice = array(
        'ForClients' => 'Цена для клиентов',
        'ForVIP' => 'Цена для VIP',
        'ForShares' => 'Цена по акции-абонементу',
    );

	// Add to cart
	// -----------
	public function actionAddToCart($args = array()) {

        $itemsList = $args['products'];

        $cartClass = \Core::getClass('cart');
        $nodeClass = \Core::getClass('node');
        $clientClass = \Core::getClass('client');

        $client = $clientClass::findPK($_SESSION['client']);

		// Append items
		// ------------
		foreach($itemsList as $itemID => $item) {

            $count = @$item['count'];
            $cart = $cartClass::findOne(array('query' => array('client' => @$_SESSION['client'], 'product' => $itemID, 'confirmed' => array('$ne' => true))));

            if (empty($cart)) $cart = new $cartClass();

            $product = $nodeClass::findPK($itemID);

            $price = @$product->data['price'.@$client->statusPrice];

			$count = (int) $count;
			if ($count == 0) {
                $cart->delete();
			}
			else {
                $cart->client = $_SESSION['client'];
                $cart->product = $itemID;
                $cart->count = @$count;
                $cart->price = @$price;
                $cart->date = time();
                $cart->save();
			}

		}

		// Send update event
		// -----------------
		\Events::send('shoppingCartUpdated', array(), array('client' => true));

        return \Widgets::get('node', array('id' => @$itemID, 'mode' => $args['mode'], 'args' => array('client' => $client)));

	}

	// Clear cart
	// ----------
	public function actionClearCart() {
		$_SESSION['shop-cart'] = array();
		\Events::send('shoppingCartUpdated', null, array('client' => true));
	}


	// Order
	// -----
	public function actionOrder() {
		\Popups::show('shop-order');
	}

	// Update cart list
	// ----------------
	public function actionUpdateCartList() {

		return array(
			'widget' => \Widgets::get('shop-cart-list')
		);
	}
}
