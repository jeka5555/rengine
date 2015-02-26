<?php
namespace YandexMaps\Widgets;


class YandexMap extends \Widget {


	// Component
	// ---------
	public static $component = array(
		'id' => 'yandex-map',
		'title' => 'Карта Яндекс',
		'editable' => true
	);

	public function getWidgetArgsFormat() {
		return array(
			'width' => array('type' => 'text', 'title' => 'Ширина карты'),
			'height' => array('type' => 'text', 'title' => 'Высота карты'),
			'address' => array('type' => 'text', 'title' => 'Адрес центра карты'),
			'location' => array('type' => 'record', 'title' => 'Координаты центра карты', 'format' => array(
				'longitude' => array('type' => 'number', 'title' => 'Долгота'),
				'latitude' => array('type' => 'number', 'title' => 'Широта'),
			)),
			'zoom' => array('type' => 'number', 'title' => 'Масштаб'),
			'overlayArray' => array('type' => 'list', 'title' => 'Список меток', 'format' => array(
				'type' => 'record', 'format' => array(
					'address' => array('type' => 'text', 'title' => 'Адрес'),
					'location' => array('type' => 'record', 'title' => 'Координаты', 'format' => array(
						'longitude' => array('type' => 'text', 'title' => 'Долгота'),
						'latitude' => array('type' => 'text', 'title' => 'Широта'),
					)),
					'balloonContent' => array('type' => 'textarea', 'isHTML' => true, 'title' => 'Текст в окошке')
				)
			))
		);
	}


	// Render
	// ------
	public function render() {

		if (isset($this->args['id'])) $id = $this->args['id']; else $id = 'map_'.$this->generateHtmlID(); // id

		// Detect size of map
		// ------------------
		$width = first_var(@$this->args['width'], '100%');
		$height = first_var(@$this->args['height'], '300px');

		// Append px to number results
		// ---------------------------
		if (is_numeric($width)) $width .= 'px';
		if (is_numeric($height)) $height .= 'px';

		if (!empty($this->args['address'])) {
			$geocode = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($this->args['address']).'&format=json&results=1'));
			if (!empty($geocode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos))
				list($longitude, $latitude) = explode(' ', $geocode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
		}
		
		if (empty($longitude)) $longitude = first_var(@$this->args['location']['longitude'], 37.64); // Долгота
		if (empty($latitude)) $latitude = first_var(@$this->args['location']['latitude'], 55.76); // Широта
		

		
		
		$zoom = first_var(@$this->args['zoom'], 6); // Масштаб

		// Элементы управления [typeSelector, mapTools, zoomControl, MiniMap, ScaleLine, SearchControl...]
		// -------------------------------
		$controlArray = first_var(@$this->args['controlArray'], array('zoomControl', 'mapTools'));

    	// Метки [['position' : 'Center', 'draggable' : 'true'], ['location' : ['longitude' : 37.64, 'latitude' : 55.76], 'draggable' : 'false']]
		$overlayArray = first_var(@$this->args['overlayArray'], false);

		// Добавление элементов управления
		// -------------------------------
		$addControl = '';
		if (!empty($controlArray)) {
			foreach($controlArray as $item)	$addControl .= 'map.controls.add("'.$item.'");';
		}

		// Пользовательские стили
		// ----------------------------
		$addStyle = '';
		if (isset($this->args['style'])) {
			foreach($this->args['style'] as $styleID => $item) {
				$addStyle .= '
					var '.$styleID.' =
						options.iconImageHref: "'.@$item['icon']['img'].'", // картинка иконки
	                    iconImageSize: ['.@$item['icon']['width'].', '.@$item['icon']['height'].'], // размеры картинки
                	}
                ';
			}
		}

		// Добавление меток
		// ---------------------------
		$addOverlay = '';
		if (!empty($overlayArray)) {

			foreach($overlayArray as $key => $item) {
				// Позиция метки (по-умолчанию по центру)
				if (isset($item['location']['longitude']) and isset($item['location']['latitude'])) {
					$position = '['.$item['location']['longitude'].', '.$item['location']['latitude'].']';
				}
				elseif (isset($item['address'])) {
					$geocode = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($item['address']).'&format=json&results=1'));
					if (!empty($geocode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos))
						$position = '['.str_replace(' ', ', ', $geocode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos).']';
				}
				else {
					$position = 'map.getCenter()';
				}

				// Свойства
				// -----------------------------
				$properties = 'var properties = {};';

				// Текст всплывающего окошка
				if (@$item['balloonContent']) $properties .= 'properties.balloonContent = "'.$item['balloonContent'].'"';


				// Опции
				// -----------------------------
				$options = 'var options = {};';

		     	// Можно-ли переносить метки
		     	// -----------------------------
		     	if (@$item['draggable']) $options .= 'options.draggable = true';

		     	// Задан ли стиль метки
		     	// ДОДЕЛАТЬ!!!!
		     	// -----------------------------
		     	/*
					 if(!@$item['style']) $options .= '
		     		options.iconImageHref = "/modules/eda_ru/template/images/overlay.png"
		     		options.iconImageSize = [25, 32]
		     		options.iconImageOffset = [-15, -25] // смещение картинки
		     	';
		     	*/

				$addOverlay .= '
					'.@$properties.'
					'.@$options.'
					// Добавление объекта (метки) в коллекцию
					myCollection.add(new ymaps.Placemark('.@$position.', properties, options));
				';
			}
			$addOverlay .= '
				// Добавление коллекции на карту
				map.geoObjects.add(myCollection);
			';
		}

		$content = '
			<script type="text/javascript" src="http://api-maps.yandex.ru/2.0/?coordorder=longlat&load=package.full&wizard=constructor&lang=ru-RU&onload=fid_13437333310308324820"></script>
			<div id="'.$id.'" style="width:'.$width.'; height:'.$height.'"></div>';

		$script = '
			var map, geoResult;

            ymaps.ready(function() {
				// Пользовательские стили
				'.@$addStyle.'

				// Создание экземпляра карты и его привязка к созданному контейнеру
				map = new ymaps.Map("'.$id.'", {center: ['.$longitude.', '.$latitude.'], zoom: '.$zoom.'});

				// Добавление элементов управления
				'.@$addControl.'

				// Создаем коллекцию геообъектов.
				var myCollection = new ymaps.GeoObjectCollection();

				// Добавление меток на карту
				'.@$addOverlay.'

        });';

		// Submit script
		// -------------
		\Events::send('addEndScript', $script);

		// Return content
		// --------------
		return $content;
	}
}
