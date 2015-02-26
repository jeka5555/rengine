<?php

namespace Shop\Widgets;


class Order extends \Widget {


	// Component
	// ---------
	public static $component = array(
		'id' => 'order',
		'title' => 'Оформление заказа',
		'editable' => true
	);

	// Render
	// ------
	public function render() {
	
		$orderForm = \Widgets::get('form', array('id' => 'shop-order'));
		
		$content = '
			<div class="widget-order-header">
				<div class="widget-order-title">Оформление заказа</div>
			</div>
			'.$orderForm.'
			<div class="widget-order-footer">
				<button class="button gray-button close-order has-icon"><span class="icon"></span><span class="title">назад</span></button>
				<button class="button green-button order-button has-icon"><span class="icon"></span><span class="title">отправить</span></button>
			</div>
		';

	  \Events::send('addEndScript', '
	  
			// Отправка заказа
			$(".widget-order .order-button").click(function(){
				$(this).closest(".ui-dialog-content").find("form").submit();
			});
			                                
			// Закрытие окна заказа
			$(".widget-order .close-order").click(function(){
				$(this).closest(".ui-dialog-content").dialog("close");
			});
			
		');


		// Return content
		// --------------
		return $content;
	}
}
