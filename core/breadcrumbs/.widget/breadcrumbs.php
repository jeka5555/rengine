<?php

namespace RAN\Widgets;


class Breadcrumbs extends \Widget {

	// Component
	// ---------
	public static $component = array(
		'id' => 'breadcrumbs',
		'title' => 'Хлебные крошки',
		'editable' => true
	);

	// Widget args format
	// ------------------
	public function getWidgetArgsFormat() {
		return array(
			'breadcrumbsID' => array('type' => 'text', 'title' => 'Идентификатор хлебных крошек')
		);
	}

	// Render breadcrumbs
	// ------------------
	public function render() {

		// We need items
		// -------------
		if (empty($this->args['breadcrumbsID'])) $this->args['breadcrumbsID'] = 'default';

		// Get items
		// -----------------------
		$breadcrumbsModule = \Core::getModule('breadcrumbs');
		if (empty($breadcrumbsModule)) {
			\Logs::log(array('text' => 'Отсутствует модуль хлебных крошек'), 'warning');
			return;
		}

		// Get items
		// ---------
		$items = $breadcrumbsModule->getBreadcrumbs($this->args['breadcrumbsID']);
		if (empty($items)) return;

		// BBuild content
		// --------------
		$itemsList = array();
		foreach($items as $item) {

			$link = '';
			$activeAddin = '';

			// Make link addition
			// ------------------
			if (\Core::getModule('sites')->currentNode !== @ $item['id'] and @$item['active'] !== true) {
				if (!empty($item['link'])) $link = ' href="'.$item['link'].'" ';
                $tag = 'a';
			} else {
				$activeAddin = 'active';
                $tag = 'span';
			}

			// One item
			// --------
			$itemsList[] = '<'.$tag.' class="breadcrumb '.$activeAddin.'" '.$link.'>'.$item['title'].'</'.$tag.'>';
		}

		// Build content
		// -------------
		$content = join('<span class="divider">/</span>', $itemsList);
		return $content;

	}
}