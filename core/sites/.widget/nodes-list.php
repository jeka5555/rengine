<?php

namespace Core\Widgets;

class NodesList extends \Core\Widgets\ListWidget {

	// Component
	// ---------------------
	public static $component = array(
		'id' => 'nodes-list',
		'editable' => true,
		'title' => 'Список узлов',
	);

	// Get widget args format
	// ----------------------
	public function getWidgetArgsFormat() {

		return array(

			// Query data
			// ----------
			'type' => array('title' => 'Тип ноды', 'allowEmpty' => true, 'type' => 'component', 'componentType' => 'node'),
			'parent' => array('type' => 'object', 'class' => 'node', 'title' => 'Корневой узел'),
			'query' => array('type' => 'record', 'title' => 'Фильтрация'),
			'sort' => array('type' => 'list', 'title' => 'Сортировка', 'format' => array(
				'type' => 'record', 'mode' => 'full', 'format' => array(
					'field' => array('type' => 'text', 'title' => 'Поле'),
					'order' => array('type' => 'number', 'title' => 'Порядок'),
				)
			)),

			// Display format
			// --------------
			'showTitle' => array('type' => 'boolean', 'title' => 'Отображать заголовок', 'value' => true),
			'title' => array('type' => 'text', 'title' => 'Название блока'),

			// Mode
			// ----
			'mode' => array('type' => 'text', 'title' => 'Режим отображения'),
			'listType' => array('type' => 'select', 'title' => 'Тип списка', 'values' => array(
				'div' => 'div',
				'ul' => 'ul'
			)),

			// Pagination
			// ----------
			'usePaginator' => array( 'type' => 'boolean', 'title' => 'Отображать страницы', 'value' => true),
			'page' => array('type' => 'number', 'title' => 'Текущая страница'),
			'pageLimit' => array('type' => 'number', 'title' => 'Максимум на странице'),
		);




	}

	// Render paginator
	// ----------------
	private function renderPaginator() {


		if (@$this->usePaginator != true) return;

		$content = '';

		// Страничная навигация
		// --------------------
		$childrenCount = $this->getChildren($this->current, true);

		if ($childrenCount > $this->pageLimit) {

			// Resource list widget is here
			// ---------------------------
			$nodeListWidget = $this;

			$paginator = \Core::getModule('widgets')->createWidget(array(
				'paginator',
				array(
					'count' => ceil($childrenCount / $this->pageLimit),
					'active' => $this->page,
					'function' => @ $this->args['pageFunction']
				)
			));

			// Add page navigation
			// -------------------
			$content .= $paginator->get();
		}

		return $content;

	}

	// Render title
	// ------------
	private function renderTitle() {
		$content = '<h2  class="title">'.$this->args['title'].'</h2>';
		return $content;
	}


	// Get children node by query
	// ------------------------------
	public function getChildren($node = null, $count = false) {

		// Get class
		// ---------
		$nodeClass = \Core::getComponent('class', 'node');
		if (empty($nodeClass)) return;

		// Base request
		// ------------
		$query = array('hidden' => array('$ne' => true));

		// Set parent
		// ----------
		if (!empty($this->args['parent'])) $query['parent'] = $this->args['parent'];

		// Filter by other values
		// ----------------------
		if (!empty($this->query) && is_array($this->query)) {
			$query = array_merge($query, $this->query);
		}

		// Filter by type
		// --------------
		if (!empty($this->args['type'])) $query['type'] = $this->args['type'];

		// Sort
		// -----
		if (!empty($this->sort)) {
			foreach($this->sort as $key => $item) {
				if (!empty($item['field']) and !empty($item['order'])) {
					$sort[$item['field']] = $item['order'];
				} else {
					$sort[$key] = $item;
				}
			}		
		}
		
		$limit = null; $skip = 0;

		// Если не подсчет
		// ---------------
		if ($count != true)  {

			// Limit
			// -----
			if (!empty($this->pageLimit)) $limit = $this->pageLimit;
			if (!empty($this->page)) $skip = $this->pageLimit * ($this->page - 1 );
		}
		
		// Get nodes
		// ---------
		$nodes = call_user_func(array($nodeClass, 'find'), array('query' => $query, 'limit' => $limit, 'skip' => $skip, 'count' => $count, 'sort' => @$sort));

		return $nodes;

	}


	// Render list of resource
	// -----------------------
	public function render() {

		// Mode
		// ----
		$this->mode = first_var(@ $this->args['mode'], 'preview');

		// Queries
		// -------
		$this->sort = first_var(@ $this->args['sort'], array());
		$this->query = first_var(@ $this->args['query'], array());

		// Pagination
		// ----------
		$this->usePaginator = first_var(@ $this->args['usePaginator'], true);
		$this->pageLimit = first_var(@ $this->args['pageLimit'], 20);
		$this->page = first_var(@ $this->args['page'], 1);
		$this->parent = @ $this->args['parent'];

		// Возврат контента
		// ----------------
		return $this->renderContent();
	}

	// Render content
	// --------------
	public function renderContent() {

		// Collect content here
		// --------------------
		$content = '';

		// Show header
		// -----------
		if (!empty($this->args['title']) && @ $this->showTitle !== false) {
			$content = $this->renderTitle().$content;
		}

		$outerTag = first_var(@$this->args['outerTag'], 'div');
		$innerTag = first_var(@$this->args['innerTag'], 'div');
		if (@ $this->args['listType'] == 'ul') {
			$outerTag = 'ul';
			$innerTag = 'li';
		}
        if (@ $this->args['listType'] == 'table') {
            $outerTag = 'table';
            $innerTag = 'tr';
        }

		// Get list of resources
		// ---------------------
		if(isset($this->args['nodes'])) {
			$nodes = $this->args['nodes'];
		} else {
			$nodes = $this->getChildren();
		}
		 
		if (!empty($nodes)) {

			$listContent = '';

            if (!empty($this->args['headingRow'])) $listContent = $this->args['headingRow'];

			// Add children elements
			// ---------------------
			foreach($nodes as $index => $node) {

				$listContent .= \Widgets::get('node', array( 'id' => $node->_id, 'mode' => $this->mode, 'index' => $index, 'args' => @$this->args),
					array(
						'htmlClasses' => array('nodes-list-item'),
						'tag' => $innerTag
					));
			}

			// Merge content
			// -------------
			$content .= '<'.$outerTag.' class="nodes-list">'.$listContent.'</'.$outerTag.'>';
		}


		// Render paginator
		// ----------------
		$content .= $this->renderPaginator();

		// If nothing in content, cancel
		// -----------------------------
		if (empty($content)) {
			$this->cancel();
		}

		// Show content
		// ------------
		return $content;

	}


}
