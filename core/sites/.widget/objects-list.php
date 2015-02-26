<?php

namespace Core\Widgets;

class ObjectsList extends \Core\Widgets\ListWidget {

	// Component
	// ---------------------
	public static $component = array(
		'id' => 'objects-list',
		'editable' => true,
		'title' => 'Список объектов',
	);

	// Get widget args format
	// ----------------------
	public function getWidgetArgsFormat() {

		return array(

			// Query data
			// ----------
			'class' => array('title' => 'Класс объекта', 'allowEmpty' => true, 'type' => 'text'),
			'queryJSON' => array('type' => 'list', 'title' => 'Фильтрация', 'hint' => 'Фильтры должны быть правильной JSON строкой', 'format' => array(
				'type' => 'text', 'title' => 'Запрос' 
			)),
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
		if(isset($this->args['objects'])) {
			$childrenCount = count($this->args['objects']);
		} else {
			$childrenCount = $this->getChildren($this->current, true);
		}

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
	public function getChildren($object = null, $count = false) {

		// Get class
		// ---------
		$objectClass = \Core::getClass($this->args['class']);
		if (empty($objectClass)) return;

		// Base request
		// ------------
		$query = array();

		// Filter by other values
		// ----------------------
		if (!empty($this->args['queryJSON']) && is_array($this->args['queryJSON'])) {
			foreach($this->args['queryJSON'] as $item) {
				$addinQuery = json_decode($item, true);   
				if (is_array($addinQuery)) {
					$query = array_merge($query, $addinQuery);
				}				
			}			
		}
		
		if (!empty($this->args['query']) && is_array($this->args['query'])) {
			$query = array_merge($query, $this->args['query']);		
		}
		
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
		$objects = call_user_func(array($objectClass, 'find'), array('query' => $query, 'limit' => $limit, 'skip' => $skip, 'count' => $count, 'sort' => $this->sort));

		return $objects;

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
		$this->queryJSON = first_var(@ $this->args['queryJSON'], array());

		// Pagination
		// ----------
		$this->usePaginator = first_var(@ $this->args['usePaginator'], true);
		$this->pageLimit = first_var(@ $this->args['pageLimit'], 10);
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

		// Get list of objects
		// ---------------------
		if(isset($this->args['objects'])) {
			$objects = $this->args['objects'];
		} else {
			$objects = $this->getChildren();
		}
		
		
		if (!empty($objects)) {

			$listContent = '';

			// Add children elements
			// ---------------------
			foreach($objects as $index => $object) {
				$listContent .= \Widgets::get('object', array( 'object' => $object, 'mode' => $this->mode, 'index' => $index),
					array(
						'htmlClasses' => array('objects-list-item'),
						'tag' => $innerTag
					));
			}

			// Merge content
			// -------------
			$content .= '<'.$outerTag.' class="objects-list">'.$listContent.'</'.$outerTag.'>';
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
