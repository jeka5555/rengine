<?php

namespace Modules;

// Основной класс
// ------------------------------
class Media extends \Module {

	// Component data
	// --------------
	public static $component = array(
		'id' => 'media',
		'title' => 'Медиа',
		'hasSettings' => true
	);


	public static $componentSettingsFormat = array(

		// Restrictions
		// ------------
		'maxImageWidth' => array('type' => 'number', 'title' => 'Максимальная ширина изображения'),
		'maxImageHeight' => array('type' => 'number', 'title' => 'Максимальная высота изображения'),
        'fileUploadAccess' => array('type' => 'rules', 'title' => 'Правила доступа'),
		'maxUploadedFileSize' => array('type' => 'number', 'title' => 'Максимальная размер загружаемого файла'),
		'maxSpace' => array('type' => 'number', 'title' => 'Лимит дискового пространства'),
		'cropUploadedImages' => array('type' => 'boolean', 'title' => 'Обрезка изображений при загрузке'),
		'mediaDirectory' => array('type' => 'text', 'title' => 'Директория для файлов'),
		'tempDirectory' => array('type' => 'text', 'title' => 'Директория для временных файлов'),
	);

	// Settings
	// --------
	public static $settings = array(

		// Restrictions
		// ------------
		'maxImageWidth' => 2048,
		'maxImageHeight' => 2048,
		'maxUploadedFileSize' => 128000000,
		'maxSpace' => null,
		'cropUploadedImages' => true,

		// Media defaults
		// --------------
		'defaultUploadFolder' => null,

		// Direcories
		// ----------
		'tempDirectory' => 'media/temp',
		'mediaDirectory' => 'media'
	);

	// Get structure of editor
	// -----------------------
	public function getComponentEditorStructure() {

		return array(

			// General parameters
			// ------------------
			array('type' => 'block', 'title' => 'Директории',
				'elements' => array(
					array('type' => 'form', 'properties' => array('tempDirectory', 'mediaDirectory'))
				)
			),

			// Authorization
			// -------------
			array('type' => 'block', 'title' => 'Обработка изображений',
				'elements' => array(
					array('type' => 'form', 'properties' => array('cropUploadedImages', 'maxImageWidth','maxImageHeight'))
				)
			),

			// Social authorization
			// --------------------
			array('type' => 'block', 'title' => 'Загрузка',
				'elements' => array(
					array('type' => 'form', 'properties'  => array('fileUploadAccess', 'maxSpace', 'maxUploadedFileSize'))
				)
			)
			,
		);

	}

	// Download action
	// ----------------
	public function actionDownload($args = array())	{

		// Need ID
		// -------
		$mediaID = $args['mediaID'];
		if (empty($mediaID)) return;

		// Read media
		// ----------
		$mediaClass = \Core::getComponent('class', 'media');
		$media = $mediaClass::findPK($mediaID);
		if (empty($media)) return;

		// Generate uri
		// ------------
		$uri = $media->getURI();

		// Set headers
		// -----------
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $media->title . '.' . $media->fileExtension . '"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize(__DR__ . $uri));

		// Output required file
		// --------------------
		ob_clean();
		flush();
		readfile(rtrim(__DR__, '/') . $uri . '.' . $media->fileExtension);

	}

	// Получение маленькой картинки на ресурс
	// --------------------------------------
	public function actionGetCover($args = array()) {

		$mode = first_var(@$args['mode'], 'cover');

		return array(
			'preview' => \Widgets::get('media', array('mediaID' => @ $args['mediaID'], 'width' => 72, 'height' => 72, 'mode' => $mode))
		);
	}

	// Разбор пути до папки
	// -----------------------
	public function actionFolderPathProcess($args=array()) {


		// Get class
		// ---------
		$mediaFolderClass = \Core::getClass('mediaFolder');

		// Folder path must be correct
		// ---------------------------
		if (empty($args['folderPath'])) return false;

		// If string is given
		// ------------------
		if (is_string($args['folderPath'])) {
			$folderPath = explode('/', $args['folderPath']);
		}

		// If this is array
		// ----------------
		elseif (is_array($args['folderPath'])) {
			$folderPath = $args['folderPath'];
		}

		// Else
		// ----
		else {
			return false;
		}

		// Проходимся по переданному пути
		// -----------------------------
		foreach($folderPath as $item) {

			if(!empty($item) and is_string($item)) {

				// Ищем все папки на текущем уровне
				// -----------------------------
				if(empty($currentFolder))
					$folders = $mediaFolderClass::find(array('query' => array('parentID' => array('$exists' => false))));
				else
					$folders = $mediaFolderClass ::find(array('query' => array('parentID' => $currentFolder)));

				// Проверяем существует ли на текущем уровне папка с переданным именем
				// -----------------------------
				$exists = false;
				foreach($folders as $folder)
					if($folder->title == $item) {
						$currentFolder = $folder->_id;
						$exists = true;
					}

				// Create folder
				// -------------
				if($exists == false) {
					if(!empty($currentFolder)) $folderData['parentID'] = $currentFolder;
					$folderData['title'] = $item;
					$folder = $mediaFolderClass::getInstance($folderData);
					$folder->save();
					$currentFolder = $folder->_id;
				}

			}
		}

		// Get last folder ID
		// ------------------
		if (!empty($currentFolder)) {
			return $currentFolder;
		}

		return false;
	}


	// Get type
	// --------
	public function getType($contentType) {

		if(in_array($contentType, array('audio/mpeg'))) return 'audio';
		if(in_array($contentType, array('video/x-flv'))) return 'video';
		if(in_array($contentType, array('image/jpeg', 'image/png', 'image/gif'))) return 'image';
		if(in_array($contentType, array('application/x-shockwave-flash'))) return 'flash';

		return 'document';
	}


	// Calculate space, used by media
	// ------------------------------
	public function actionCalculateUsedSize() {

		// Size of which directory
		// -----------------------
		$dir = __DR__.static::$settings['mediaDirectory'];

		// Get size
		// --------
		$output = exec('du -sk ' . $dir);
		$size = trim(str_replace($dir, '', $output)) * 1024;

		// Return
		// ------
		return $size;
	}

	// Upload action
	// -------------
	public function actionUpload($args = array()) {

        // check rules
        // ----------------------------------------
        if (!empty(static::$settings['fileUploadAccess'])) {
            if (!\Rules::check(static::$settings['fileUploadAccess'])) return false;
        }

		// Read data
		// ---------
		$headers = \URI::parseHeaders();

		// Prepare headers
		// ---------------
		$fileID = @$headers["X-FILE-ID"];
		if (!$fileID) return false;
		$blobID = $headers['X-FILE-PART'];
		$blobsCount = $headers['X-BLOBS-COUNT'];
		$fileName = $headers['X-FILE-NAME'];

		// Init counter of recieved parts
		// ------------------------------
		if (!isset($_SESSION['uploadedFiles'][$fileID])) {
			$_SESSION['uploadedFiles'][$fileID] = 0;
			mkdir(__DR__.'media/temp/'.$fileID, 0777);
		}

		// Read input data
		// ---------------
		$input = fopen("php://input", "r");

		// Temporary name
		// -------------------------
		$blobFileName = __DR__.static::$settings['tempDirectory'].'/'.$fileID.'/'.$blobID.'.tmp';

		// Copy data
		// ---------
		$blobFile = fopen($blobFileName, "w");
		$realSize = stream_copy_to_stream($input, $blobFile);

		// Close temp file
		// -----------------------
		fclose($blobFile);

		// Increase parts counter
		// ----------------------
		$_SESSION['uploadedFiles'][$fileID] += 1;

		// If not all parts are received, return just success
		// --------------------------------------------------
		if ($_SESSION['uploadedFiles'][$fileID] != $blobsCount) {
			$result = array('success' => true, 'status' => 'mediaPartUploaded', 'fileID' => $fileID, 'blobID' => $headers['X-FILE-PART']);
			return $result;
		}

		// Create new media object
		// -----------------------
		$mediaClass = \Core::getComponent('class', 'media');
        $mediaID = (string)new \MongoID();

		// Name of final file
		// ------------------
		$destFile = fopen(__DR__.static::$settings['mediaDirectory'].'/'.$mediaID, "wb");

		// Join all file parts
		// -------------------
		for($blobID = 0; $blobID < $blobsCount; $blobID++) {
			$blobFileName = __DR__.'media/temp/'.$fileID.'/'.$blobID.'.tmp';
			$blobFile = fopen($blobFileName,"rb");
			fwrite($destFile, fread($blobFile, filesize($blobFileName)));
			fclose($blobFile);
			unlink($blobFileName);
		}

		// Remove file's temp folder
		// -------------------------
		rmdir(__DR__.static::$settings['tempDirectory'].'/'.$fileID);

		// Определяем имя и расширение
		// -------------------
		$mediaPath = __DR__.static::$settings['mediaDirectory'].'/'.$mediaID;
		$pathinfo = pathinfo($fileName);
		$filename = urldecode($pathinfo['filename']);
		$ext = $pathinfo['extension'];
		$size = filesize($mediaPath);
		$contentType = mime_content_type($mediaPath);

		// Add correct file's extension
		// ----------------------------
		rename($mediaPath, $mediaPath.'.'.$ext);

		// Close final file
		// ----------------
		fclose($destFile);

		// Fill media with data
		// --------------------
		$media = $mediaClass::getInstance(
			array(
				'_id' => $mediaID,
				'title' => $filename,
				'fileSize' => $size,
				'fileExtension' => $ext,
				'contentType' => $contentType,
				'type' => @ self::getType($contentType)
			)
		);

		// Add folder
		// ----------
		$folderID = @$headers["X-FOLDER-ID"];
		if (!empty($folderID)) {
			$media->folderID = $folderID;
		}

		// Save
		// ----
		$media->save();

		// Init postprocessing
		// -------------------
		$media->postUpload();

		// Return result
		// -------------
		$result = array('success' => true, 'status' => 'mediaUploaded', 'id' => $mediaID);
		return $result;


	}

	// Module install
	// --------------
	public function installModule() {
		mkdir(__DR__.static::$settings['mediaDirectory'], 0777);
		mkdir(__DR__.static::$settings['tempDirectory'], 0777);
	}

}


