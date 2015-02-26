<?php

namespace Core\Media\Classes;

class Media extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'media',
		'title' => 'Медиа',
		'editable' => true
	);

	// Real media object
	// -----------------
	public $object = null;

	// Class properties
	// ----------------
	public static $classProperties = array(

		// Main data
		// ---------
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true),
		'type' => array('type' => 'select', 'title' => 'Тип медиа', 'values' => array(
			'image' => 'Изображение',
			'audio' => 'Аудио',
			'video' => 'Видео',
			'document' => 'Документ',
			'youtube' => 'YouTube',
			'vimeo' => 'Vimeo',
			'flickr' => 'Изоображение с flickr',
			'simple' => 'Локальный файл'), 'listing' => true, 'sortable' => true),
		'folderID' => array('type' => 'object', 'class' => 'mediaFolder', 'title' => 'Папка'),
		'alias' => array('type' => 'text', 'title' => 'Краткий идентификатор'),

		// File-related data
		// -----------------
		'fileExtension' => array('type' => 'text', 'title' => 'Расширение', 'listing' => true, 'sortable' => true),
		'fileSize' => array('type' => 'number', 'editable' => false, 'title' => 'Размер файла', 'listing' => true, 'sortable' => true),
		'uri' => array('type' => 'text', 'title' => 'Путь до файла', 'listing' => true, 'sortable' => true),
		'contentType' => array('type' => 'text', 'title' => 'Тип контента', 'editable' => false),

		// Other
		// -----
		'hidden' => array('type' => 'boolean', 'title' => 'Скрыто'),
		'state' => array('type' => 'select', 'title' => 'Состояние', 'allowEmpty' => true, 'values' => array('uploading' => 'Загружается', 'ready' => 'Готов', 'notExits' => 'Не существует')),
		'uploadProgress' => array('type' => 'number', 'title' => 'Статус загрузки')

	);

	// Editor blocks
	// -------------
	public static  $classEditorBlocks = array(
		'default' => array('title' => 'Основные свойства файла'),
		'file' => array('title' => 'Свойства файла', 'asTab' => true, 'properties' => array('fileExtension', 'fileSize', 'uri', 'contentType') ),
		'additional' => array('title' => 'Дополнительно', 'asTab' => true, 'properties' => array('isVisible', 'state', 'uploadProgress'))
	);


	// Class access
	// ------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'read' => array(array('type' => 'true'))
	);

	// Class actions
	// -------------
	public static $classActions = array(
		'rename' => array('title' => 'Переименовать'),
		'createObject' => array('title' => 'Создать', 'classAction' => true),
	);

	// Create media instance
	// ---------------------
	public static function getInstance($data = array()) {

		// Create base
		// -----------
		$proxy = new static($data);

		// Get type
		// --------
		if (!empty($data['type'])) {
			
			if ($class = first_var(@ \Core::getComponent('media-type', @ $data['type']), @ \Core::getComponent('media-type', 'simple'))) {

				$objectClass = $class;

				// Create object
				// -------------
				$object = new $objectClass();
				$object->set($data);

				// Add object to proxy
				// -------------------
				$proxy->object = $object;
				$proxy->object->properties = & $proxy->properties;

			}
		}

		// Return complete object
		// ----------------------
		return $proxy;

	}

	// Call unknown function
	// ---------------------
	public function __call($name, $args) {
		if (!empty($this->object))
			return call_user_func_array(array($this->object, $name), $args);
	}

	// Delete
	// ------
	public function delete() {
	
		foreach (glob(__DR__.'media/' . $this->_id . '*') as $file) {
			@unlink($file);
		}
		
		static::$component['id'] = 'media';
		parent::delete();
		\Events::send('objectDelete', array('class' => 'media', 'id' => $this->_id), array('client' => true));
		return true;
	}

	// Save action
	// -----------
	public function save($args = array()) {
		static::$component['id'] = 'media';
		if(@$this->properties['isVisible'] != true) $this->isVisible = false;
		parent::save();
		\Events::send('objectUpdate', array('class' => 'media', 'id' => $this->_id), array('client' => true));
		return true;
	}

	// Safe save method
	// ----------------
	public static function safeFindAndSave($data=array()) {
		$media = self::getInstance($data);
		$media->save();
		return true;
	}

	// Rename action
	// -------------
	public function actionRename($args = array()) {

		// Open object editor
		// ------------------
		\Events::send('addScript',
		'new Apps.start("objectEditor", {
			title : "Переименование",
			id : "'.$this->_id.'",
			class : "'.static::$component['id'].'",
			fields : ["title"]
		});');
		return true;
	}

	// Switch visibility action
	// ------------------------
	public function actionSetVisibility($val) {
		$this->isVisible = $val;
		$this->save();
	}

}
