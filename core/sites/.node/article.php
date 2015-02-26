<?php

namespace Core\Nodes;

class Article extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'article',
		'title' => 'Статья'
	);

	// Article resource format
	// -----------------------
	public static function getNodeDataFormat() {
		return array(
			'text' => array('type' => 'contentEditor', 'title' => 'Основной текст'),
			'introText' => array('type' => 'text', 'title' => 'Краткий текст', 'input' => 'textarea', 'isHTML' => true),
			'image' => array('type' => 'media', 'title' => 'Основная картинка'),
			'isPromo' => array('type' => 'boolean', 'title' => 'Отображать в блоке промо-статей'),
		);
	}

	// Render full
	// -----------
	public function renderModeFull() {

		$content = '<h1>'.$this->title.'</h1>';
		if (!empty($this->data['text'])) $content .= '<div class="text">'.\DataView::get('content', $this->data['text']).'</div>';

		return $content;
	}

	// Render full
	// -----------
	public function renderModePreview() {


		$content = '';

		// Add image
		//  --------
		if (!empty($this->data['image'])) {
			$content .= '<div class="image">'.\DataView::get('media', $this->data['image'], array('width' => 370, 'height' => 125, 'mode' => 'cover' )).'</div>';
		}

		// Title
		// -----
		$content .= '<a href="'.$this->getURI().'"><h2 class="title">'.$this->title.'</h2></a>';

		// Add intro text
		// --------------
		if (!empty($this->data['introText'])) {
			$content .= '<div class="intro-text">'.\DataView::get('text', $this->data['introText']).'</div>';
		}

		// Link
		// ----
		$content .=  '<div class="more-link"><a href="'.$this->getURI().'">подробнее</a></div>';

		return $content;
	}

	public function renderModeSearchResult() {
		$content = '
			<h3><a href="'.$this->getURI().'">'.$this->title.'</a></h3>
			<div class="text">'.\DataView::get('content', @ $this->data['text'], array('textMode' => true, 'stripTags' => true, 'maxChars' => 200)).'</div>
		';
		return $content;
	}

}
