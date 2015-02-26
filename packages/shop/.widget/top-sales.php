<?php

namespace Shop\Widgets;


class TopSales extends \Widget {


	// Component
	// ---------
	public static $component = array(
		'id' => 'top-sales',
		'title' => 'Хиты продаж',
		'editable' => true
	);
	
	
	public function getWidgetArgsFormat() {
		return array(
			'limit' => array('type' => 'number', 'title' => 'Лимит товаров'),
		);
	}


	// Render
	// ------
	public function render() {

  	$limit = first_var(@$this->args['limit'], 9);

		$content = '<h2>Хиты продаж</h2>';


		// Search top sales nodes
		// ----------------------
		$nodeClass = \Core::getClass('node');
		
		// Берём в первую очередь с галочкой "выводить в хитах продаж"
		$nodes = $nodeClass::find(array('query' => array(
			'type' => 'product',
			'data.showTopSales' => true,
			'hidden' => array('$ne' => true),
		), 'sort' => array('data.saleCount' => -1), 'limit' => $limit));	

		
		// Если не хватает берём остальные самые покупаемые товары
		if (count($nodes) < $limit) {
			
			$limit = $limit - count($nodes);

			$nodes = array_merge($nodes, $nodeClass::find(array('query' => array(
				'type' => 'product',
				'hidden' => array('$ne' => true),
			), 'sort' => array('data.saleCount' => -1), 'limit' => $limit)));
		}


		// Add nodes to content
		// --------------------
		if (!empty($nodes)) {

			$childrenContent = '';

			// Get widget of each node
			// -----------------------
			foreach ($nodes as $node) {
				$childrenWidget = \Core::getModule('widgets')->createWidget(array('node', array(
					'id' => $node->_id,
					'mode' => 'hit'
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
