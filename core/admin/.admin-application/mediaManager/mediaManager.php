<?php

namespace Core\Admin\AdminApplications;

// Класс приложения для медиа-менеджера
// ------------------------------------
class MediaManager extends \Core\Admin\Components\AdminApplication {

	// Информация о компоненете
	// ------------------------
	public static $component = array(
		'id' => 'mediaManager',
		'title' => 'Файлы',
		'icon' => '/core/admin/.admin-application/mediaManager/icon.png',
		'access' => array(
			array(
				'type' => 'or',
				'rules' => array(
					array('type' => 'userRole', 'role' => 'super'),
					array('type' => 'userRole', 'role' => 'administrator'),
					array('type' => 'userRole', 'role' => 'content-manager')
				)
			)
		),
		'addOnToolbar' => true
	);

	// Получение информации об узле
	// --------------------
	private static function getPathItem($item) {


		// Folder Class
		// ------------
		$folderClass = \Core::getComponent('class', 'mediaFolder');
		$folder = $folderClass::findPK($item['id']);

		// Если ничего нет, то облом
		// -------------------------
		if (empty($folder)) return false;
		if (empty($folder->parentID)) return false;

		// Parent
		// -----
		$parent = $folderClass::findPK($folder->parentID);
		return array('id' => $parent->_id, 'title' => first_var($parent->title, "Новая папка"));
	}


	// Получение маленькой картинки на ресурс
	// --------------------------------------
	public function commandGetPreview($args = array()) {

		$mode = first_var(@$args['mode'], 'cover');
		return array(
			'preview' => \Widgets::get('media', array('mediaID' => @ $args['mediaID'], 'width' => @ $args['width'], 'height' => @$args['height'], 'mode' => $mode))
		);
	}

	// Полная информация о папке
	// -------------------------
	public function commandGetFolderContent($args = array()) {

		$folderClass = \Core::getComponent('class', 'mediaFolder');
		$folders = array();

		// Извлечение папок
		// ----------------
		if (!empty($args['folderID'])) {
			$foldersQuery = array('parentID' => $args['folderID']);
			$thisFolder = $folderClass::safeFindPK($args['folderID']);
		}
		else {
			$foldersQuery = array('$or' => array(
				array('parentID' => null),
			));
		}

		// Извлекаем
		// ---------
		$readFolders = $folderClass::safeFind(array('query' => $foldersQuery));
		if (!empty($readFolders)) {
			foreach($readFolders as $folder) {
				$folders[] = array('title' => $folder->get('title'), '_id' => $folder->get('_id'), 'parent' => $folder->get('parentID'));
			}
		}

		// Построение пути
		// ---------------
		$path = array();
		if (!empty($args['folderID'])) {

		// Добавляем текущую папку
		$thisFolder->properties['disabled'] = true;
		$path[] = $thisFolder->properties;

			// Начинаем с этого узла
			// ---------------------
			$pathItem = array('id' => $args['folderID']);
			while ($pathItem = @ static::getPathItem($pathItem)) {
				$path[] = $pathItem;
			}

		}

		// Корень
		// ------
		$path[] = array('title' => '.');
		$path = array_reverse($path);

		// Извлечение медиа
		// ----------------
		if (!empty($args['folderID'])) $mediaQuery = array('folderID' => $args['folderID']);
		else $mediaQuery = array('$or' => array(
			array('folderID' => null),
		));

		// Выбор медиа
		// -----------
		$mediaClass = \Core::getComponent('class', 'media');
		$media = $mediaClass::safeFind(array('query' => $mediaQuery, 'accessMode' => 'edit'));
		$resultMedia = array();

		// Генерация превью
		// ----------------
		if (!empty($media)) {
			foreach ($media as $elementID => $element) {
      	switch($element->type) {
					case 'image':                                   
						$preview = \Widgets::get('media', array('mediaID' => $element->_id, 'width' => 64, 'height' => 64, 'mode' => 'cover'), array('tag' => false));
						break;
					default:  
						if (file_exists(self::$component['componentPath'].'/img/'.$element->type.'.png')) {
							$preview = '<img src="'.self::$component['componentPath'].'/img/'.$element->type.'.png">';
						} else {
							$preview = '<img src="'.self::$component['componentPath'].'/img/default.png">';
						}             						
						break;
				}
				$resultMedia[] = array('_id' => @ $element->_id, 'title' => @ $element->title, 'preview' => $preview);
			}
		}

		// Отправка
		// --------
		$result = array(
			'folders' => $folders,
			'media' => $resultMedia,
			'path' => $path
		);

		return $result;
	}
}