<?php

namespace Shop;

class Order extends \Form {

	// Component
	// ---------
	public static $component = array(
		'type' => 'form',
		'id' => 'shop-order',
		'title' => 'Форма заказа'
	);

	// Format
	// ------
	public $format =  array(
		'fio' => array('type' => 'text', 'title' => 'Ваше имя', 'validator' => array('required')),
		'phone' => array('type' => 'text', 'title' => 'Телефон', 'validator' => array('required')), 
		'email' => array('type' => 'text', 'title' => 'E-mail', 'placeholder' => 'e-mail', 'validator' => array('email', 'required')),
		'comment' => array('type' => 'textarea', 'title' => 'Комментарий'),
	);

	// Submit
	// ------
	public function submit() {

		// Validate
		// --------
		$result = parent::submit();
		if ($result !== true) return $result;
		
		// Save order
		// ------
		$orderClass = \Core::getClass('order');
		foreach($_SESSION['shop-cart'] as $key => $val) {
			$products[] = array('id' => $key, 'count' => $val);		
		}		
		$orderObject = new $orderClass(array(
			'name' => $this->value['fio'],
			'phone' => $this->value['phone'],
			'products' => $products,
			'date' => time(), 
		));
		$orderObject->save();
		
		// Submit mail
		// ------
		$this->mailTo();		
		
		// Clear cart
		// ----------
		$_SESSION['shop-cart'] = array();
		
		// Popup data
		// ----------
		$popupData = array(
			'dialogClass' => 'site-popup',
			'title' => 'Заказ принят',
			'content' => '
				<div>Ваш заказ успешно отправлен, мы скоро свяжемся с вами!</div>
				<div class="button-panel">
					<a class="button green-button close-button" href="/"><span class="title">закрыть</span></a>
				</div>
			',
			'modal' => true,
			'minimizable' => false,
			'collapsable' => false,
			'resizable' => false,
		);
		$popupID = 'popup'.uniqid();
		\Events::send('addScript', '
			var '.$popupID.' = new Popup('.json_encode($popupData).');

			'.$popupID.'.window.close = function() {
				window.location = "/";	
			}
		');

	}
	
	
	public function renderMailContent() {
	
		$result = parent::renderMailContent();
		
		// Node class
		// ----------
		$nodeClass = \Core::getClass('node');

		// Get count and total price
		// -------------------------
		if (!empty($_SESSION['shop-cart'])) {
			$allSumm = 0;
			foreach($_SESSION['shop-cart'] as $productID => $productCount) {
				$productNode = $nodeClass::findPK($productID);

				// Увеличиваем счётчик продаж
				$productNode->data['saleCount'] = @$productNode->data['saleCount'] + $productCount;
				$productNode->save();

				$productSumm = @ $productNode->data['price'] * $productCount;
				$allSumm = $allSumm + $productSumm;

				$productNode = \Node::getNodeObject($productNode);

				@$orderInfo .= '
					<tr>
						<td><a href="'.$productNode->getURI().'">'.$productNode->title.'</a></td>
						<td>'.@ $productNode->data['price'].' р.</td>
						<td>'.$productCount.'</td>
						<td>'.$productSumm.' р.</td>
					</tr>
				';
			}
			$orderInfo = '
				<table cellpadding="5" border="1">
					<tr>
						<th>Наименование</th>
						<th>Цена за 1 шт.</th>
						<th>Количество</th>
						<th>Общая стоимость</th>
					</tr>
					'.$orderInfo.'
					<tr>
						<th colspan="3"></th>
						<th>'.$allSumm.' р.</th>
					</tr>
				</table>
			';
		}
		
		$result .= '<h2>Информация о заказе:</h2>'.$orderInfo;
		
		return $result;	
	}

}
