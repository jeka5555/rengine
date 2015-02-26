<?php

namespace Core\Nodes;

class News extends \Node {

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


	// Node data format
	// ----------------
	public static function getNodeDataFormat() {
		return array(
			'text' => array('type' => 'text', 'title' => 'Основной текст', 'input' => 'textarea', 'isHTML' => true),
			'introText' => array('type' => 'text', 'title' => 'Краткий текст', 'input' => 'textarea', 'isHTML' => true),
			'image' => array('type' => 'media', 'title' => 'Изображение', 'folderPath' => array('Новости')),
		);
	}

	// Render short mode
	// -----------------
	public function renderModePreview() {

		$content ='
		<div class="date">'.\DataView::get('datetime', @ $this->properties['@createTime']).'</div>
		<div class="content">
			<div class="introText">'.\DataView::get('text', @ $this->data['introText']).'</div>
			<a class="more-link" href="'.$this->getURI().'">подробнее</a>
		</div>';


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
		$content = '
			<h3><a href="'.$this->getURI().'">'.$this->title.'</a></h3>
			<div class="text">'.\DataView::get('text', @ $this->data['text'], array('stripTags' => true, 'maxChars' => 200)).'</div>
		';
		return $content;
	}
}
