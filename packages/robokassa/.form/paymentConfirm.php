<?php

namespace Forms;

class PaymentConfirm extends \Form {

	// Регистрация компонента	
	// ----------------------
	public static $component = array(
		'type' => 'form',
		'id' => 'paymentConfirm',
		'title' => 'Подтверждение оплаты через Робокассу'
	);

	// Информация о форме
	// -------------------
	public $format = array(
		array('id' => 'name', 'type' => 'text', 'title' => 'Имя', 'placeholder' => 'имя', 'validator' => array('required')),
		array('id' => 'phone', 'type' => 'text', 'title' => 'Телефон', 'placeholder' => 'телефон', 'validator' => array('required')),
		array('id' => 'address', 'type' => 'textarea', 'title' => 'Адрес доставки', 'placeholder' => 'адрес', 'validator' => array('required')),
		array('id' => 'summ', 'type' => 'hidden', 'hidden' => true),
		array('id' => 'count', 'type' => 'hidden', 'hidden' => true),
		array('id' => 'typeSize', 'type' => 'hidden', 'hidden' => true),
		array('id' => 'kitCount', 'type' => 'hidden', 'hidden' => true),
	);
	
	public $buttons = array(
		array('id' => 'submit', 'type' => 'submit', 'title' => 'Перейти к оплате')
	);


	// Отправка информации из формы
	// ----------------------------
	public function submit() {
	
		// Проверка данных на валидность
		// -----------------------------
		$calc = current(\Objects\Widget::find(array('query' => array('type' => 'calc'))));
		
		foreach($calc->widget->args['typeSizes'] as $item) {
			if($item['size'] == $this->value['typeSize']) {
				$price = $item['price'];
				continue;
			}
		}
		if(@$price) {
			$summ = ($price * $this->value['count']) + ($calc->widget->args['kitPrice'] * $this->value['kitCount']); 
			if($summ != $this->value['summ']) {
				\Client::submitMessage(array('text' => 'В процессе оплаты произошла ошибка. Неверная сумма!'));
				return false;
			}
		} else {
			\Client::submitMessage(array('text' => 'В процессе оплаты произошла ошибка. Не правильный типоразмер!'));	
			return false;
		}

		// Проверка данных на валидность
		// -----------------------------
		$ordersClass = \Core::getComponent('class', 'orders');
		$orders = new $ordersClass();
		
		$orders->name = @$this->value['name']; 
		$orders->phone = @$this->value['phone']; 
		$orders->address = @$this->value['address']; 
		$orders->date = time();
		
		$orders->summ = @$this->value['summ'];
		$orders->count = @$this->value['count'];
		$orders->typeSize = @$this->value['typeSize'];
		$orders->kitCount = @$this->value['kitCount'];
		$orders->isPaid = false;

		if($order = $orders->save())
			\Robokassa::paymentRedirect($order, $this->value);
				
	}

}

