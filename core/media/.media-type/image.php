<?php

namespace Core\Media\MediaTypes;

class Image extends \Core\Media\MediaTypes\Simple {

	// Component
	// ---------
	public static $component = array(
		'type' => 'media-type',
		'id' => 'image',
		'title' => 'Изображения'
	);
	
	// Post upload function
	// --------------------
	public function postUpload() {
	
		$mediaModule = \Core::getModule('media');
	
		// Если нужно обрезать изображения при загрузке
		// ----------------
		if($mediaModule::$settings['cropUploadedImages']) {
			$image = new \Image(array(
				'sourceFile' => __DR__.$mediaModule::$settings['mediaDirectory'].'/'.$this->_id.'.'.$this->fileExtension,
				'targetFile' => __DR__.$mediaModule::$settings['mediaDirectory'].'/'.$this->_id.'.'.$this->fileExtension,
				'rewriteFile' => true,
				'effects' => array(
					array(
						'type' => 'resize',
						'width' => $mediaModule::$settings['maxImageWidth'],
						'height' => $mediaModule::$settings['maxImageHeight'],
						'scaleMode' => 'contain'
					)
				)
			));
		}
		
		parent::postUpload();
	}

	// Get link
	// --------
	public function getURI() {

		// Base link
		// ---------
		$fileName = 'media/'.$this->_id;

		// Apply effects
		// -------------
		if (empty($this->effects)) {
			if (!is_null($this->width) || !is_null($this->height)) {
				$this->effects = array(array(
					'type' => 'resize',
					'width' => $this->width,
					'height' => $this->height,
					'scaleMode' => $this->mode
				));
			}
		}

		// Если хэш существует
		// -------------------
		if (!empty($this->effects)) {

			// Проверяем хэш
			// -------------
			$hash = \Image::generateEffectsHash($this->effects);

			// Если хэш, пробуем эффекты
			// -------------------------
			if (!empty($hash)) {

				// Создание нового имени
				// ---------------------
				$newFileName = $fileName.'-'.$hash;

				// Если файл не существует, обработка
				// --------------------------------
				if (!file_exists(__DR__.$newFileName.'.'.$this->fileExtension)) {

					$image = new \Image();
					$image->load(__DR__.$fileName.'.'.$this->fileExtension);
					$image->process($this->effects);
					$image->save(__DR__.$newFileName.'.'.$this->fileExtension);

				}

				$fileName = $newFileName;
			}

		}

		// Возврат относительной ссылки
		// ---------------------------
		return '/'.$fileName.'.'.$this->fileExtension;

	}

	// Render function
	// ---------------
	public function renderMedia() {

		// Detect URI
		// ----------
		$fileName =	$this->getURI();

		// If true, return it
		// ------------------
		if(@$this->getURI) return $fileName;

        if (!empty($this->title)) $addTitle = ' title="'.$this->title.'"';

		// Or return a source image
		// ------------------------
		$content = '<img'.@$addTitle.' class="media-type-image" data-media-id="'.$this->_id.'" src="'.$fileName.'" />';
		return $content;
	}

}
