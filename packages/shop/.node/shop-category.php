<?php

namespace Shop\Nodes;

class ShopCategory extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'shop-category',
		'title' => 'Магазин.Категория'
	);

	// product node format
	// -----------------------
	public static function getNodeDataFormat() {
		return array(
			'text' => array('type' => 'textarea', 'isHTML' => true, 'title' => 'Описание', 'searchable' => true),
			'image' => array('type' => 'media', 'title' => 'Изображение', 'mediaType' => 'image', 'folderPath' => array('Магазин', 'Категории')),
			'price' => array('type' => 'number', 'title' => 'Цена'),
            'isCases' => array('type' => 'boolean', 'title' => 'Это кейс')
		);
	}
	
	public function renderModeMenuItem() {
		$content = '<a href="'.$this->getURI().'" class="category-menu-item item-'.@$this->path.'"></a>';
		return $content;
	}



}
