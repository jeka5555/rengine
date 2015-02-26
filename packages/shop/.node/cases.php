<?php

namespace Shop\Nodes;

class Cases extends \Node{

	// Component
	// ---------
	public static $component = array(
		'id' => 'cases',
		'title' => 'Магазин.Кейс',
	);

	// product node format
	// -----------------------
	public static function getNodeDataFormat() {
	
		$shopComponent = \Core::getModule('shop');

		return array(
            'image' => array('type' => 'media', 'title' => 'Изображение', 'mediaType' => 'image', 'folderPath' => array('Магазин', 'Кейсы')),
            'contents' => array('type' => 'list', 'title' => 'Содержимое', 'format' => array(
                'type' => 'object', 'class' => 'node', 'title' => 'Продукт или услуга'
            )),
			'smallText' => array('type' => 'textarea', 'isHTML' => true, 'title' => 'Краткое описание', 'searchable' => true),
		);
	}


    public function getURI() {

        // Get node path
        // -------------
        $nodePath = first_var(@ $this->path, @ $this->_id);

        $path = \URI::parseURI($_SERVER['REQUEST_URI']);

        return '/'.implode('/', array_slice($path, 0, 6)).'/'.$nodePath;

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

        $nodeClass = \Core::getClass('node');

        if (!empty($order->count)) $count = $order->count;

        // Если это продукт
        if (!isset($this->data['service'])) {
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

        // Если это сервис
        } else {

            if ($count > 0) {
                $button = '<button class="button grey-button remove-from-cart"><span class="title">убрать</span></button>';
            } else {
                $button = '<button class="button grey-button update-to-cart"><span class="title">добавить</span></button>';
            }

            $controls = '
            <input type="hidden" class="count-product" value="1">
            <div class="buttons">'.$button.'</div>';
        }

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

        if (!empty($this->data['contents'])) {
            foreach($this->data['contents'] as $item) {
                $item =  $nodeClass::findPK($item);
                @$contents .= '<div>'.@$item->title.'</div>';
            }
        }

        if (!empty($this->data['price'])) {
            $priceContent =  '<div class="price"><span class="shop-item-data-value">'.\DataView::get('number', first_var(@ $this->data['price'], '-'), array('divider' => ' ')).'</span> руб.</div>';
        }

        $content = '
			<div class="product-image">
				'.$image.'
			</div>
			<div class="desc">
				<div class="title">'.$this->title.'</div>
				<div class="smallText">'.$this->smallText.'</div>
				'.@$priceContent.'
				<div class="contents">
				    <h3>В состав набора входит:</h3>
				    '.@$contents.'
                </div>
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

        $content = '
			<h1>'.$this->title.'</h1>
			<div class="product-image">
				'.\DataView::get('media', @ $this->data['image'], array('width' => 230, 'height' => 155, 'mode' => 'cover')).'
			</div>
			<div class="desc">'.@ $this->data['text'].'</div>
			<div class="buy">
				<span class="price"><span class="shop-item-data-value">'.\DataView::get('number', first_var(@ $this->data['price'], '-'), array('divider' => ' ')).'</span> руб.</span>
				<input type="text" class="count-product input-spinner" value="'.$count.'" />
				<button class="button green-button update-to-cart has-icon"><span class="icon"></span><span class="title">добавить</span></button>
			</div>
		';

        return $content;
    }


    public function renderModeInСart() {


        $count = 0;

        $orderClass = \Core::getClass('order');
        $order = $orderClass::findOne(array('query' => array('client' => @$_SESSION['client'], 'product' => $this->_id, 'confirmed' => array('$ne' => true))));

        if (!empty($order->count)) $count = $order->count;

        // Если это продукт
        if (!isset($this->data['service'])) {
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

        // Если это услуга
        } else {
            $controls = '
            <input type="hidden" class="count-product" value="'.$count.'">
            <div><a class="calendar-icon" href="/clients/view/'.$_SESSION['client'].'/plan/'.$order->_id.'">запланировать</a></div>
            ';
        }

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

        // Если это услуга
        if (isset($this->data['service'])) {
            $planButton = '<div><a class="button grey-button" href="/clients/view/'.$_SESSION['client'].'/plan/'.$order->_id.'">запланировать</a></div>';
        }

        $content = '
			<td class="desc">'.$this->title.'</td>
			<td>'.$count.' шт.</td>
			<td>
			    '.@$planButton.'
			    <button class="button grey-button confirm-purchase"><span class="title">подтвердить</span></button>
            </td>
		';

        return $content;

    }

}
