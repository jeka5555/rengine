<?php

namespace Core\Admin\AdminApplications;

class PackageManager extends \Core\Admin\Components\AdminApplication {

	// Component
	// ---------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'packageManager',
		'title' => 'Диспетчер пакетов',
		'access' => array(
			array('type' => 'userRole', 'role' => 'administrator')
		)
	);
	
	public function getPackagesDir() {
		return __DR__.'packages/';
	}
	
	public static function initComponent() {
		self::$component['icon'] = '/'.self::$component['componentPath'].'/icon.png';
		parent::initComponent();
	}

	// Init manager with data
	// ----------------------
	public function commandInit($args = array()) {

		$packageClass = \Core::getClass('package');

		// Get download modules
		// -------------------------
		if (is_dir($this->packagesDir)) {

			$modulesFolder = scandir($this->packagesDir);
			foreach($modulesFolder as $packageID) {
			
				$moduleInfo = array();
			
				$package = $packageClass::findOne(array('query' => array('id' => $packageID))); 

				// Если пакет уже установлен и активен
				// ----------------------------
				if (!empty($package) and @$package->enable === true) {
					$currentPackageClass = \Core::getComponent('package', $package->id);
					$moduleInfo = $currentPackageClass::$component;
					$moduleInfo['enable'] = true;
					$moduleInfo['actions']['uninstall'] = true; 
					$initData['downloadModules'][] = $moduleInfo;    				
				} 

				// Если пакет не установлен или неактивен, то проверяем наличие файла package.php с информацией о пакете и забираем из него эту инфу 
				// ----------------------------
				else if ($className = $this->getPackageClassName($packageID)) { 
					$moduleInfo = $className::$component;	

					// Если пакет установлен, но не активен
					// -------------------------
					if (!empty($package)) {
						$moduleInfo['enable'] = false;		
					} else {
						$moduleInfo['actions']['init'] = true;
						$moduleInfo['enable'] = null;
					}
					
					$moduleInfo['actions']['uninstall'] = true; 							
					$initData['downloadModules'][] = $moduleInfo; 
				}

			}

		}


		// Get repository modules
		// -------------------------
		$modulesFolder = array(

		);

		foreach($modulesFolder as $moduleInfo) {

			if (!empty($moduleInfo['title'])) {
				$moduleInfo['actions']['install'] = true;
		  	$initData['repositoryModules'][] = $moduleInfo;
		  }
		}


		// Return init data
		// -------------------------
		return $initData;


	}

	// Activate Package
	// ----------------------
	public function commandEnablePackage($package = array()) {

		$packageClass = $this->getPackageClassName(@ $package['id']);
		if ($packageClass == false) return 'Неизвестный клаcс пакета';
		$result = $packageClass::enablePackage();

		return $result;
	}

	// Unactivate Package
	// ----------------------
	public function commandDisablePackage($package = array()) {

  	$packageClass = $this->getPackageClassName(@$package['id']);
		if ($packageClass == false) return 'Неизвестный клаcс пакета';
		$result = $packageClass::disablePackage();

		return $result;
	}


	// Init Package
	// ----------------------
	public function commandInitPackage($package = array()) {

		$packageClass = $this->getPackageClassName(@ $package['id']);
		if ($packageClass == false) return 'Неизвестный клаcс пакета';
		$result = $packageClass::installPackage();

		return $result;
	}

	// Delete Package
	// ----------------------
	public function commandUninstallPackage($package = array()) {

  	$packageClass = $this->getPackageClassName(@$package['id']);
		if ($packageClass == false) return 'Неизвестный клаcс пакета';
		$result = $packageClass::uninstallPackage();

		return $result;
	}
	
	
	// Get package class name
	// ----------------------
	public function getPackageClassName($packageID) {

  	if(empty($packageID)) return false;

		$packageClass = \Core::getComponent('package', $packageID);
		if(!empty($packageClass)) return $packageClass;
	
		else if (file_exists($this->packagesDir.$packageID.'/package.php')) {
	
		  require_once($this->packagesDir.$packageID.'/package.php');
			$updatedClasses = get_declared_classes();
			$updatedClassesCount = count($updatedClasses);
			
			for($position = \Loader::$classesCount ; $position < $updatedClassesCount; $position ++ ) {
					
					$className = $updatedClasses[$position];
	
					// Если включенный класс - пакет
					// ---------------------------------------------
					if (is_subclass_of($className, '\Package') ) {
						$classNamePackage = $className;
						break;
					}
			}
			
			// Store component position
			// ------------------------
			\Loader::$classesCount = $updatedClassesCount;

		}
		
		if(!empty($classNamePackage)) return $classNamePackage;

		return false;
	}


}
