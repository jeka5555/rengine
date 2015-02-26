<?php

namespace Search\Nodes;

class Search extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'search',
		'title' => 'Поиск по сайту'
	);


	// Render
	// ------
	public function renderModeFull() {

		// Get current page
		// ----------------
		$this->page = first_var(@ $_GET['page'], 1);


		// Heading
		// -------
		$content =  '<h1>Результаты поиска</h1>';

		// Build string
		// ------------
		$searchString = @ $_REQUEST['query'];

		$query = array(
			'hidden' => array('$ne' => true),
			'$or' => array(
				array('title' => new \MongoRegex("/.*".$searchString.".*/iu")),
				array('data.text' => new \MongoRegex("/.*".$searchString.".*/iu"))
			)
		);

		// Collect searchable types
		// ------------------------
		$types = array('article', 'news', 'product', 'shop-category');
		$query['type'] = array('$in' => $types);

		// Result
		// ------
		$nodeClass = \Core::getClass('node');


		// Get count
		// ---------
		$count = $nodeClass::find(array(
			'query' => $query,
			'sort' => array('@createTime' => 1),
			'count' => true
		));

		// Render result
		// -------------
		if ($count > 0) {

			// Get page
			// --------
			$result = $nodeClass::find(array(
				'query' => $query,
				'sort' => array('@createTime' => 1),
				'skip' => 50 * ($this->page - 1),
				'limit' => 50
			));

			// Collect items content
			// ---------------------
			$itemsContent = '';
			foreach($result as $node) {
				$nodeObject = \Node::getNodeObject($node);
				$itemsContent .= '<div class="result-item">'.$nodeObject->render('searchResult').'</div>';
			}

			// Append result
			// -------------
			$content .= '<div class="result-items">'.$itemsContent.'</div>';


			// Render paginator
			// ----------------
			if ($count > 50) {

				// Vars
				// ----
				$node = $this;
				$id = $this->_id;

				// Render paginator
				// ----------------
				$paginator = \Core::getModule('widgets')->createWidget(array(
					'paginator',
					array(
						'count' => ceil($count / 50),
						'active' => $this->page,
						'function' => function($page) use($id, $node) {
							$request = array();
							$request['page'] = $page;
							return $node->getURI().'?'.http_build_query($request);
						}
					)
				));

				$content .= $paginator->get();

			}
		}

		// Nothing is found
		// ----------------
		else {
			$content .= '<p>ничего не найдено</p>';
		}




		// Return
		// ------
		return $content;

	}
}
