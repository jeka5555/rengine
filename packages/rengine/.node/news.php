<?php

namespace Himsintez\Nodes;

class News extends \Core\Nodes\News {

	// Component
	// ---------
	public static $component = array(
		'id' => 'news',
		'title' => 'Новость'
	);

	// Node settings
	// -------------
	public static $nodeSettings = array(
		'isRenderable' => true
	);


	// Render short mode
	// -----------------
	public function renderModePreview() {

		$content = '
    <div class="news_item table">
    	<div class="table_cell news_item_image">
        '.\DataView::get('media', $this->data['image'], array('width' => 95, 'height' => 95, 'mode' => 'cover' )).'
    	</div>
    	<div class="table_cell news_item_text">
    		<div class="news_item_date">'.\DataView::get('datetime', @ $this->properties['@createTime']).'</div>
    		<a class="news_item_link" href="'.$this->getURI().'">'.\DataView::get('text', @ $this->title).'</a>
    	</div>
    </div>
    ';


		return $content;
	}


	// Render short mode
	// -----------------
	public function renderModeFull() {

		$content = '
			<div class="date">'.\DataView::get('datetime', @ $this->properties['@createTime']).'</div>
			<h1>'.$this->title.'</h1>
			<div class="text">'.@ $this->data['text'].'</div>
		';

		return $content;
	}


	// Render for search query
	// -----------------------
	public function renderModeSearchResult() {
		$content = $this->renderModePreview();
		return $content;
	}
}
