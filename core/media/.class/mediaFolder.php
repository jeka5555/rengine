<?php

namespace Media\Objects;

class MediaFolder extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type'	 => 'class',
		'id' => 'mediaFolder',
		'title' => 'Папка медиа',
		'editable' => false
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'parentID' => array('type' => 'object', 'class' => 'mediaFolder', 'title' => 'Родительская папка', 'listing' => true, 'sortable' => true, 'searchable' => true),
		'readOnly' => array('type' => 'boolean', 'title' => 'Только для чтения', 'default' => false),
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'true'))
	);

	// Class actions
	// -------------
	public static $classActions = array(
		'rename' => array('title' => 'Переименовать'),
		'createObject' => array('title' => 'Создать', 'classAction' => true),
	);


	// Свой метод для удаления
	// -----------------------
	public function delete() {

		// Обязательно должен быть реальный объект
		// ---------------------------------------
		if (empty($this->_id)) return;

		// Удаляем подпапки
		// ---------------
		$subfolders = static::find(array('query' => array('parentID' => $this->_id)));

    	if(!empty($subfolders))
			foreach ($subfolders as $folder) $folder->delete();

		// Обновляем медиа
		// ---------------
		$mediaClass = \Core::getComponent('class', 'media');
		$mediaClass::findAndUpdate(array('folderID' => $this->_id), array('folderID' => false));

		// Удаляем себя
		// ------------
		parent::delete();
	}


	public function actionRename($args = array()) {
		\Events::send('addScipt', '
				new Apps.start("objectEditor", {
					title : "Переименование",
					id : "'.$this->_id.'",
					class : "'.static::$component['id'].'",
					fields : ["title"]
				});
		');
		return true;
	}


}
