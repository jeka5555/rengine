<?php

namespace Shop\Widgets;


class RelatedProducts extends \Widget {


	// Component
	// ---------
	public static $component = array(
		'id' => 'relatedProducts',
		'title' => 'Сопутсвующие товары',
		'editable' => true
	);
	
	
	public function getWidgetArgsFormat() {
		return array(
			'productID' => array('type' => 'object', 'class' => 'node', 'title' => 'Продукт'),
			'limit' => array('type' => 'number', 'title' => 'Лимит товаров'),
			'mode' => array('type' => 'text', 'title' => 'Режим отображения товаров', 'value' => 'preview'),
		);
	}


	// Render
	// ------
	public function render() {

  	$limit = first_var(@$this->args['limit'], 9);

		$content = '<h2>С этим товаром также покупают</h2>';


		$nodeClass = \Core::getClass('node');
		

		$connection = \DB::$connection;
		$result = \DB::$connection->order->aggregate(array(
			array('$match' => array('products.id' => @$this->args['productID'])), // Выбираем все заказы с выбранным продуктом 
			array('$unwind' => '$products'),                                       //	Разбираем поле с продуктами на документы  
			array('$group' => array('_id' => '$products.id', 'total' => array('$sum' => '$products.count'))), // Группируем по продуктам и его сумме покупок
			array('$match' => array('_id' => array('$ne' => @$this->args['productID']))), // Отбрасываем сам продукт
			array('$sort' => array('total' => -1)), // Сортируем по убыванию общего числа покупок
		));    
		if(@$result['ok'] == true and !empty($result['result'])) {
			
			$childrenContent = '';
		
		  foreach($result['result'] as $item) {
				$childrenWidget = \Core::getModule('widgets')->createWidget(array('node', array(
					'id' => $item['_id'],
					'mode' => first_var(@$this->args['mode'], 'preview')
				)));
				$childrenContent .= $childrenWidget->get();	
								
			}  
			
			$content .= '<div class="items">'.$childrenContent.'</div>';
		}
           
		// Return content
		// --------------
		return $content;
	}
}
