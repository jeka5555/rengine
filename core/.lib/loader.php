<?php

// Система автоматической загрузки файлов
// --------------------------------------
class Loader {

	// Статистическая информация
	// ------------------------
	public static $hasCompiledJS = false;
	public static $hasCompiledCSS = false;
	public static $classesCount = 0;
	public static $currentDir = '/';
	public static $moduleDir = '/';
	public static $loadedPackages = array();

	public static $history = array(); // Loading history
	public static $packagePath = array(); // Full path to current sub module
	public static $subpackagePath = array(); // Path to current submodule
	public static $componentPath = array(); // Path to component
	public static $componentFiles = array(); // Путь компонента

	// Типы и порядок включения элементов
	// ----------------------------------
	public static $types = array(
		'class',
		'assets',
        'api'
  );

	// Список загруженных файлов, по типам
	// -----------------------------------
	public static $files = array(
		'css' => array(),
		'js' => array(),
		'php' => array()
	);

	// Файл, которые не должны быть загружены
	// -------------------------------------
	public static $skipList = array();

	// Получение относительной ссылки на файл
	// --------------------------------------
	private static function getRelativeURI($path) {
		$resourcePath = substr(str_replace('\\', '/', $path), strlen(__DR__));
		if($resourcePath[0] == '/') return $resourcePath;
		else return '/'.$resourcePath;
	}	


	// Обработка инструкций в файле обработки
	// --------------------------------------
	private static function parseIncludeFile($path) {
	
		// Читаем файл
		// -----------	
		$importFile = __DR__ . $path . '/.import.xml';
		if (file_exists($importFile)) {

			$data = simplexml_load_file($importFile);

			// Skip list
			// ---------
			if (!empty($data->skip)) {
				foreach ($data->skip->children() as $object) {
					static::$skipList[] = $path.'/'.$object;
				}
			}

			// Import files
			// ------------
			if (!empty($data->import)) {
				foreach ($data->import->children() as $object) {

					// Get by type
					// -----------
					switch ($object->getName()) {
						case "file":
							$object = array('as' => 'file', 'path' => $path . '/' . (string)$object);
							break;
						case "directory":
							$object = array('as' => 'directory', 'path' => $path . '/' . (string)$object);
							break;
					}

					self::importObject($object);
				}
			}

		}
	}


	// Импорт PHP-файлов
	// -----------------
	private static function importFilePHP($file) {

		// Get component path
		// ------------------
		$componentPath = substr(dirname($file), strlen(__DR__));
		array_push(\Loader::$componentPath, $componentPath);
		array_push(\Loader::$componentFiles, $file);

		// Include file
		// ------------
		include_once($file);

		// Register classes
		// ----------------
		$updatedClasses = get_declared_classes();
		$updatedClassesCount = count($updatedClasses);

		// Itterate over all files
		// -----------------------
		for($position = static::$classesCount ; $position < $updatedClassesCount; $position ++ ) {
	
			$className = $updatedClasses[$position];

			// If this is component - add
			// --------------------------
			if (is_subclass_of($className, '\Component') ) {
				\Components::addComponent($className);
			}
		}

		// Store component position
		// ------------------------
		static::$classesCount = $updatedClassesCount;

		// Remove current path
		// -------------------
		array_pop(\Loader::$componentPath);
		array_pop(\Loader::$componentFiles);

	}


	// Загрузка объектов
	// -----------------
	public static function importObject($data = array(), $basePath = null) {

		// Если не определен тип или загрузчик, то выход
		// ---------------------------------------------
		if (isset($data['as']) && method_exists('Loader', 'import'. $data['as'])) {
			call_user_func(array('Loader', 'import'.$data['as']), $data);
			return;
		}
	
		// Если директория, включаем как директорию
		// ----------------------------------------
		if (!empty($data['path'])) {
			if (is_dir(__DR__ . $data['path'])) self::importDirectory($data);
			else if (is_file(__DR__ . $data['path'])) self::importFile($data);
		}
		
	}

	// Работа с элементами
	// =============================================================

	// Включение одного файла
	// ----------------------
	public static function importFile($data = array()) {

		if (is_string($data)) $data = array('path' => $data);
	
		// Путь до файла
		// -------------
		$file = __DR__.$data['path'];
		if (!file_exists($file)) return;

		// Skip objects
		// ------------
		if(in_array($file, self::$skipList)) return;

		// Skip files with tilde
		// ---------------------
		$baseName = basename($file);
		if ($baseName[0] == '~') return;

		// Get file extension
		// ------------------
		$relativePath = self::getRelativeURI($file);
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		// Если файл отсуствует или находится в списке блока, пропускаем
		// -------------------------------------------------------------
		if (empty(self::$files[$extension])) self::$files[$extension] = array();
		if (in_array($relativePath, self::$files[$extension])) return;

		// Включение файлов при помощи плагина
		// -----------------------------------
		if (method_exists('\Loader', 'importFile'.$extension)) {
			call_user_func(array('\Loader', 'importFile'.$extension), $file);
		}

		// Другие файлы
		// ------------
		else self::$files[$extension][] = $relativePath;

	}


	// Import components from folder
	// -----------------------------
	public static function importComponents($args = array()) {
			
		// If we have component
		// --------------------
		$componentClass = @ \Core::getComponent('component', $args['type']);

		// ------------------
		if (!empty($componentClass)) {
			$componentClass::loadComponentsDirectory($args);
		}

		// Or just load as plain directory
		// -------------------------------
		\Loader::importDirectory(array('path' => $args['path']));

	}

	// Загрузка всех файлов из указанного пути
	// ---------------------------------------
	public static function importDirectory($data = array()) {

		// Путь должен быть реалным и не пустым
		// -------------------------------------
		if (empty($data['path'])) return;
		if (!file_exists($data['path'])) return;

		if(in_array($data['path'], self::$skipList)) return;

		// Skip itesm with tilde
		// -----------------------
		$baseName = basename($data['path']);
		if ($baseName[0] == '~') return;

		// Set current dir
		// ---------------
		self::$currentDir = $data['path'];
		
		// Read import instructions
		// ------------------------
		self::parseIncludeFile($data['path']);

		// Load all files from this folder
		// --------------------------------
		$files = glob(__DR__.$data['path'].'/*.*');			
		if (!empty($files)) {
			foreach($files as $file) {
				self::importObject(array('as' => 'file', 'path' => substr($file, strlen(__DR__))));
			}
		}
	}


	// Load package
	// ------------
	public static function importPackage($args = array()) {

		// If we have module path
		// ----------------------
		if (is_string($args)) {

			// Full path to module
			// -------------------
			if (strpos($args, 'core') === 0) $path = join('/', explode('.', $args));
			else $path = 'packages/'.join('/', explode('.', $args));

			// Module settings
			// ---------------
			$data = array('path' => $path, 'id' => $args);

		}

		// Or take all args as module params
		// --------------------------------
		else $data = $args;

		// Get path
		// --------
		$path = @ $data['path'];
		if (empty($path)) return;

		// Skip loaded modules
		// -------------------
		if (array_key_exists($path, \Loader::$loadedPackages)) return;
		\Loader::$loadedPackages[$path] = true;

		// Submodule path
		// --------------
		self::$subpackagePath[] = $path;

		// Is first level module?
		// ----------------------
		$isCorePackage = false;
		
		if (substr($path, 0, 8) == 'packages' || substr($path, 0, 6) == 'themes') $isCorePackage = true;
		if ($isCorePackage) {
			self::$packagePath[] = $path;
		}

		// Detect module ID
		// ----------------
		if (!empty($data['id'])) $fullPackageID = $data['id'];
		else $fullPackageID = str_replace("/", ".", $path);

		// Module ID
		// ---------
		$pid = explode(".", $fullPackageID);
		$packageID = end($pid);
		if (empty($packageID)) $packageID = $fullPackageID;

		// Import main file
		// ----------------
		$mainPackageFile = $path.'/package.php';

		if (file_exists(__DR__ . $mainPackageFile)) {
			self::importObject(array('as' => 'file', 'path' => $mainPackageFile));
		}
	
		// Import main file
		// ----------------
		$mainModuleFile = $path.'/'.$packageID.'.php';
  	    if (file_exists(__DR__.$mainModuleFile)) {
			self::importObject(array('as' => 'file', 'path' => $mainModuleFile));
		}

		// Import local module files
		// -------------------------
		self::importDirectory(array('path' => $path));

		// Load single modules
		// -------------------
		self::importDirectory(array('path' => $path.'/.component'));

		// Import all required component types
		// -----------------------------------
		foreach (self::$types as $type) {

			// Add element and add it to history
			// ---------------------------------
			$typePath = $path.'/.'.$type;
			self::$history[] = $typePath;

			// Import component types
			// ----------------------
			self::importComponents(array('type' => $type, 'path' => $typePath));

			// Remove one element from history
			// -------------------------------
			array_pop(self::$history);
		}

		// Load single modules
		// -------------------
		self::importDirectory(array('path' => $path.'/.module'));

		// Locate submodules
		// -----------------
		$dirs = glob( $path . "/*", GLOB_ONLYDIR);

		// Load them
		// ---------
		foreach($dirs as $directory) {
			if ($directory[0] != '.') {
				$subpackage = substr($directory, strlen($path));
				self::importObject(array('as' => 'package', 'path' => $directory, 'id' => $subpackage ));
			}
		}

		// Remove item form loadnig stack
		// ------------------------------
		array_pop(self::$subpackagePath);

		// If this is core module, remove it from stack
		// --------------------------------------------
		if ($isCorePackage) array_pop(self::$packagePath);

	}

	
}

\Loader::$classesCount = count(get_declared_classes());