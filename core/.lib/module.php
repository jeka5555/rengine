<?php
	
// Класс. Виджет
// --------------------------
class Module extends \Component {
             
	// Информация компонента
	// ---------------------
	public static $component = array(
		'id' => 'module',
		'title' => 'Модуль Rengine'
	);

	// Здесь сохраняются настройки модуля
	// ----------------------------------
	public static $defaultSettings = array();

	// Создание экземпляра модуля
	// --------------------------
	public static function getInstance($args = array()) {
		$class = get_called_class();
		$instance = new $class();
		return $instance;
	}

	// Создание экземпляра модуля
	// --------------------------
	public function getSetting($var) {
		$settings = static::$settings;	
		return @ $settings[$var];
	}


	// Инициализация
	// -------------
	public static function initComponent() {

		parent::initComponent();

		// Create module instance
		// ----------------------
		$moduleClass = get_called_class();
		$moduleInstance = $moduleClass::getInstance();

		// Add to modules collection
		// -------------------------
		$moduleID = static::$component['id'];
		\Modules::$modules[$moduleID] = $moduleInstance;

		// Init module
		// -----------
		$moduleInstance->init();
		static::$component['initialized'] = true;

		// State
		// -----
		if (@ $moduleClass::$component['useState'] == true) {
			if (!empty($_SESSION['module-'.$moduleID])) $moduleInstance->properties = & $_SESSION['module-'.$moduleID];
			else $_SESSION['module-'.$moduleID] = & $moduleInstance->properties;
		}

	}

	// Функция инициализации экземпляра
	// --------------------------------
	public function init() {
	
		// Если у объекта верный идентификатор, пробуем считать
		// ----------------------------------------------------
		$id = static::$component['id'];
		if (!empty($id)) {}
	}

	// Обработока данных
	// -----------------
	public function action($action, $data = array(), $path = null) {


		$thisClass = get_called_class();

		// Если операция найдена в списке
		// ------------------------------
		if (!empty($this->actions[$action])) {
			$actionMethod = @ $this->actions[$action]['method'];
		}

		// Определение автоматически по имени метода
		// -----------------------------------------
		else if (method_exists($this, 'action'.$action)) {
			$actionMethod = 'action'.$action;
		}

		// Иначе не найден метод
		// ---------------------
		else {
			return false;
		}

		// Правила доступа?
		// ----------------
		if (!empty(static::$component['access']['action'.ucfirst($action)])) {
			$accessRules = @ static::$component['access']['action'.ucfirst($action)];
		}

		// Выполнение команды
		// ------------------
		if (method_exists($this, $actionMethod)) {

			// Поиск правила безопасности
			// --------------------------
			if (!empty($accessRules)) {				

				// Если не было проверено правило, ничего не делаем
				// ------------------------------------------------
				if (!\Rules::check($rules)) {
					\Logs::log('Нет доступа на выполнение действия '.$action.' к классу '. @static::$component['id'], 'access');
					return false;
				}
			}

			return call_user_func(array($this, $actionMethod), $data, $path);
		}

	}

}