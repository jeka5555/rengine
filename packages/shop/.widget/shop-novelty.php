<?php

namespace Shop\Widgets;


class ShopNovelty extends \Widget {


	// Component
	// ---------
	public static $component = array(
		'id' => 'shop-novelty',
		'title' => 'Новинки',
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

		$content = '<h2>Новинки</h2>';


		// Search top sales nodes
		// ----------------------
		$nodeClass = \Core::getClass('node');
		
		// Берём в первую очередь с галочкой "выводить в новинках"
		$nodes = $nodeClass::find(array('query' => array(
			'type' => 'product',
			'data.showNovelty' => true,
			'hidden' => array('$ne' => true),
		), 'sort' => array('@createTime' => -1), 'limit' => $limit));	

		
		// Если не хватает берём остальные самые новые товары
		if (count($nodes) < $limit) {
			
			$limit = $limit - count($nodes);

			$nodes = array_merge($nodes, $nodeClass::find(array('query' => array(
				'type' => 'product',
				'hidden' => array('$ne' => true),
			), 'sort' => array('@createTime' => -1), 'limit' => $limit)));
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
					'mode' => 'novelty'
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
