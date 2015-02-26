<?php

class Image extends \Module {

  public static $component = array(
	  'type' => 'module',
	  'id' => 'image',
	  'title' => 'Обработка изображений'
  );

	public $properties = array(
		'effects' => array(),	// Инструкции для обработки
		'sourceFile' => null, // Исходный файл
		'targetFile' => null, // Файл для сохранения результата
		'quality' => 80 // Качество по-умолчанию
	);

	// Возвращает расширение файла
	// @todo Вынести эту функцию в class File от которого будет наследоваться Image и Video и Audio
	public function getExtension() {
		$pathinfo = pathinfo($this->sourceFile);
		return $pathinfo['extension'];
	}

	// Возвращает путь до файла
	// @todo Вынести эту функцию в class File от которого будет наследоваться Image и Video и Audio
	public function getURI() {
		if($URI = str_replace(__DR__, '', first_var(@$this->targetFile, @$this->sourceFile)))
			return '/'.$URI;
		else
			return false;
	}

	public function __construct($args = array()) {
		parent::__construct($args);

		if(!empty($this->sourceFile)) {

    	// Если targetFile не передан, то генерируем ему имя
			if(empty($this->targetFile)) $this->targetFile = __DR__.'media/'.md5($this->sourceFile).'-'.$this->getEffectsHash().'.'.$this->getExtension();

			// Если файл найден, то берём его сразу
			if (file_exists($this->targetFile)) $this->sourceFile = $this->targetFile;

			$this->load($this->sourceFile);

			// Если файл не найден или стоит принудительная перезапись, то обрабатываем и сохраняем
			if (!file_exists($this->targetFile) or $this->rewriteFile == true) {
				$this->process();
				$this->save();
			}

		}
	}

	public function __destruct() {
		if (!empty($this->imageResource)) {
		  $this->imageResource->clear();
		  $this->imageResource->destroy();
		}
	}

	public function getWidth() {
		return $this->imageResource->getImageWidth();
	}

	public function getHeight() {
		return $this->imageResource->getImageHeight();
	}

	// Загрузка
	// --------
	public function load($source) {
		$this->sourceFile = $source;

		if(!file_exists($this->sourceFile)) return false;

		$this->imageResource = new Imagick($this->sourceFile);

		// Определяем mime_content_type
		// --------------------------------
		$this->mime_type = mime_content_type($source);
		return $this;
	}


	public static function generateEffectsHash($args = array()) {
		if(empty($args)) return NULL;

		// Берем по одному effects
		foreach($args as $effect) {
			if(is_array($effect)) {
				ksort($effect);
				// Сортируем, переводим в JSON
				@$effectsJSON .= json_encode($effect);
			}

		}
		// Возвращаем md5 от полученой строки
		return md5($effectsJSON);
	}

	// Получает уникальный хэш для данного набора эффектов
	// ---------------------------------------------------
	public function getEffectsHash() {
		return $this->generateEffectsHash($this->effects);
	}

	// Сохраняем в файл sourceFile
	// -------------------------
	public function save($targetFile = NULL) {

		if(empty($this->imageResource)) return false;

		// Если передано имя, то переписываем
		if(is_string($targetFile))
			$this->targetFile = $targetFile;

		// Если targetFile не задан, сохраняем как sourceFile
		if(empty($this->targetFile)) $this->targetFile = $this->sourceFile;

		$this->imageResource->writeImage($this->targetFile);
	}


	// Выполнение очереди обработки
	// ----------------------------
	public function process($effects = null) {

		$effects = first_var($effects, $this->effects);

		// Берем по одному effects
		if(!empty($effects))
			foreach($effects as $effect) {
				// Определяем его тип (в поле type)
				// Ищем соотвествующий метод эффекта
				// Если есть, вызываем   
				$filterComponent = \Core::getComponent('image-filter', $effect['type']);
				
				if (empty($filterComponent)) continue;
				     
				$this->imageResource = $filterComponent::process($this->imageResource, $effect); 

			}

		return $this;
	}

}