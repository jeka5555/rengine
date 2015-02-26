<?php

namespace Core\Nodes;

class Collection extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'collection',
		'title' => 'Каталог узлов'
	);
	
	// Node data format
	// ----------------
	public static function getNodeDataFormat() {
		return array(
			'sort' => array('type' => 'list', 'title' => 'Сортировка', 'format' => array(
				'type' => 'record', 'mode' => 'full', 'format' => array(
					'field' => array('type' => 'text', 'title' => 'Поле'),
					'order' => array('type' => 'number', 'title' => 'Порядок'),
				)
			)),
		);
	}

	// Render section menu
	// -------------------
	public function renderChildren() {

		// Content block
		// -------------
		$contentBlock = first_var(\Core::getModule('content')->getSetting('contentBlock'), 'content');
		// List
		// ----
		$listWidget = $this->roleWidgets['list'];
		if (empty($listWidget)) {
			$listWidget = \Core::getModule('widgets')->createWidget(array('nodes-list'));
		}


		// Params
		// ------
		$id = $this->_id;
		$node = $this;

		// Render children list
		// --------------------
		$listWidget->args['parent'] = $this->_id;
		$currentPage = (int) first_var(@ $this->params['page'], 0);

		// Pagination
		// ----------
		$listWidget->args['page'] = $currentPage;
		
		// Sort
		// -----
		if (!empty($this->data['sort'])) {
			$listWidget->args['sort'] = $this->data['sort'];
		}

		$listWidget->args['pageFunction'] = function($page) use($id, $node) {
			$request = array();
			$request[$id]['page'] = $page;
			return $node->getURI().'?'.http_build_query($request);
		};

		// Page limit
		// ----------
		$listWidget->args['pageLimit'] = @ $this->pageLimit;

		// Get content
		// -----------
		return $listWidget->get();
	}


	// Execute
	// -------
	public function executeNode() {

		// Set options
		// -----------
		$this->page = first_var(@ $_REQUEST['page'], 0);

		parent::executeNode();
	}


	// Render full
	// -----------
	public function renderModeFull() {

		// Get content
		// -----------
		$content = '<h1>'.$this->title.'</h1>';
		$content .= $this->renderChildren();

		return $content;
	}
	
	// Render preview
	// -----------
	public function renderModePreview() {

		// Get content
		// -----------
		$content = '<a href="'.$this->getURI().'">'.$this->title.'</a>';

		return $content;
	}

}
