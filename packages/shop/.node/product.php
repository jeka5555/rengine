<?php

namespace Shop\Nodes;

class Product extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'product',
		'title' => 'Магазин.Продукт - Услуга',
	);

	// product node format
	// -----------------------
	public static function getNodeDataFormat() {
	
		$shopComponent = \Core::getModule('shop');

		return array(
            'subTitle' => array('type' => 'text', 'title' => 'Подзаголовок', 'searchable' => true),
			'smallText' => array('type' => 'textarea', 'isHTML' => true, 'title' => 'Краткое описание', 'searchable' => true),
			'text' => array('type' => 'textarea', 'isHTML' => true, 'title' => 'Описание полное', 'searchable' => true),
			'image' => array('type' => 'media', 'title' => 'Изображение', 'mediaType' => 'image', 'folderPath' => array('Магазин', 'Продукты')),
			'price' => array('type' => 'number', 'title' => 'Цена'),
			'currency' => array('type' => 'select', 'title' => 'Валюта', 'values' => @$shopComponent::$settings['currency']),
			'unit' => array('type' => 'select', 'title' => 'Единица измерения', 'values' => @$shopComponent::$settings['units']),
            'service' => array('type' => 'dependent', 'title' => 'Услуга', 'format' => array(
                'procedure' => array('type' => 'list', 'title' => 'Список процедур', 'format' => array(
                    'type' => 'record', 'title' => 'Процедура', 'mode' => 'full', 'format' => array(
                        'title' => array('type' => 'text', 'title' => 'Название'),
                        'duration' => array('type' => 'text', 'title' => 'Продолжительность (мин.)'),
                        'color' => array('type' => 'select', 'title' => 'Цвет', 'value' => 'purple', 'values' => array(
                            'purple' => 'Фиолетовый',
                            'orange' => 'Оранжевый',
                            'blue' => 'Синий',
                            'green' => 'Зелёный',
                        ))
                    )
                ))
            ))
		);
	}

    public function getIdentity() {
        return $this->title;    
    }

    public function addNodeControllerScript($args = array()) {

        // Get script properties
        // ---------------------
        $controllerArgs = array(
            'widget' => '#'.$args['widgetHtmlID'],
        );

        \Events::send('addEndScript', '
					productController'.str_replace($args['widgetHtmlID'],'-','').' = new ProductController('.json_encode($controllerArgs).');
			');

    }


    // Render small one
    // ----------------
    public function renderModePreview() {

        $count = 0;

        $orderClass = \Core::getClass('order');
        $order = $orderClass::findOne(array('query' => array('client' => @$_SESSION['client'], 'product' => $this->_id, 'confirmed' => array('$ne' => true))));

        if (!empty($order->count)) $count = $order->count;

        $controls = '
            <div>
                <div class="number-control">
                    <input type="text" class="count-product input-spinner" value="'.$count.'">
                </div>
            </div>
            <div>
                <div>
                    <button class="button action-button green-button update-to-cart has-icon"><span class="icon"></span></button>
                </div>
            </div>
        ';

        $imageURI = @static::$component['packagePath'].'/.assets/img/no-image.png';

        // Get from user's avatar
        // ----------------------
        if (!empty($this->data['image'])) {
            $mediaClass = \Core::getComponent('class', 'media');
            $image = $mediaClass::findPK($this->data['image']);
            if (!empty($image)) $imageURI = $image->getURI();
        }

        // Prepare avatar
        // --------------
        if (!empty($imageURI)) {
            $image = new \Image(array('sourceFile' => __DR__.$imageURI, 'effects' => array(
                array(
                    'type' => 'resize',
                    'width' => first_var(@ $args['width'], 153),
                    'height' => first_var(@ $args['height'], 153),
                    'scaleMode' => first_var(@ $args['mode'], 'cover')
                )
            )));

            $image = '<img class="has-border" src="'.$image->getURI().'" /></a>';
        }


        $content = '
			<div class="product-image">
				'.$image.'
			</div>
			<div class="desc">
				<div class="title"><a href="'.$this->getURI().'">'.$this->title.'</a></div>
				<div class="smallText">'.$this->smallText.'</div>
				<div class="price"><span class="shop-item-data-value">'.\DataView::get('number', first_var(@ $this->data['price'], '-'), array('divider' => ' ')).'</span> руб.</div>
			</div>
            <div class="controls">
                '.$controls.'
            </div>
		';

        return $content;
    }

    // Render small one
    // ----------------
    public function renderModeFull() {

        $count = 0;

        $orderClass = \Core::getClass('order');
        $order = $orderClass::findOne(array('query' => array('client' => @$_SESSION['client'], 'product' => $this->_id, 'confirmed' => array('$ne' => true))));

        if (!empty($order->count)) $count = $order->count;

        $controls = '
            <div>
                <div class="number-control">
                    <input type="text" class="count-product input-spinner" value="'.$count.'">
                </div>
            </div>
            <div>
                <div>
                    <button class="button action-button green-button update-to-cart has-icon"><span class="icon"></span></button>
                </div>
            </div>
            ';

        $content = '
			<h1>'.$this->title.'</h1>
			<div class="product-image">
				'.\DataView::get('media', @ $this->data['image'], array('width' => 230, 'height' => 155, 'mode' => 'cover')).'
			</div>
			<div class="desc">'.@ $this->data['text'].'</div>
            <div class="controls">
                '.$controls.'
            </div>
		';

        $this->options['htmlClasses'][] = 'white-container';

        return $content;
    }


    public function renderModeInСart() {


        $count = 0;

        $orderClass = \Core::getClass('order');
        $order = $orderClass::findOne(array('query' => array('client' => @$_SESSION['client'], 'product' => $this->_id, 'confirmed' => array('$ne' => true))));

        if (!empty($order->count)) $count = $order->count;

        $controls = '
            <div>
                <div class="number-control">
                    <input type="text" class="count-product input-spinner" value="'.$count.'">
                </div>
            </div>
            <div>
                <div>
                    <button class="button action-button green-button update-to-cart has-icon"><span class="icon"></span></button>
                </div>
            </div>
            ';

        $content = '
			<a class="product-image">
				'.\DataView::get('media', @ $this->data['image'], array('width' => 64, 'height' => 64, 'mode' => 'cover')).'
			</a>
			<div class="desc">
				<div class="title">'.$this->title.'</div>

                <div class="controls">
                    '.$controls.'
                </div>
			</div>
			<button class="button grey-button confirm-purchase"><span class="title">подтвердить</span></button>
		';

        return $content;

    }

    public function renderModeInСartTable() {

        $count = 0;

        $orderClass = \Core::getClass('order');
        $order = $orderClass::findOne(array('query' => array('client' => @$_SESSION['client'], 'product' => $this->_id, 'confirmed' => array('$ne' => true))));

        if (!empty($order->count)) $count = $order->count;

        $content = '
			<td class="desc">'.$this->title.'</td>
			<td>'.$count.' шт.</td>
			<td>
			    <button class="button grey-button confirm-purchase"><span class="title">подтвердить</span></button>
            </td>
		';

        return $content;

    }

}

