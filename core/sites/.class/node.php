<?php

namespace Core\Objects;

class Node extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'id' => 'node',
		'title' => 'Узел данных'
	);

	// Class actions
	// -------------
	public static $classActions = array(
		'openInManager' => array('title' => 'Открыть в диспетчере'),
		'createChild' => array('title' => 'Создать потомка')
	);

	// Class properties
	// ----------------
	public static $classProperties = array(

		// Main properties
		// ---------------
		'title' => array('type' => 'text', 'title' => 'Заголовок', 'listing' => true, 'hint' => 'Название узла, для идентификации его в списке', 'fulltext' => true),
		'parent' => array('type' => 'object', 'class' => 'node', 'title' => 'Родительский узел', 'hint' => 'Текущий узел будет активирован, только в том случае, если родительский узел был выполнен. Это позволяет создавать иерархические струкутуры управления'),
		'isSystem' => array('type' => 'boolean', 'title' => 'Системный узел', 'hint' => ''),
		'path' => array('type' => 'text', 'title' => 'Путь'),
		'type' => array('type' => 'component', 'title' => 'Тип узла', 'listing' => true, 'sortable' => true, 'componentType' => 'node', 'hint' => 'Различные узлы предоставляют различную функциональность и особые наборы параметров. Некоторые используются только для контроля выдачи страниц, подсчет посетителей, выдачу страницы ошибок. Для подробной информации необходимо ознакомиться с документацией по каждому типу узла.'),

		'hidden' => array('type' => 'boolean', 'title' => 'Невидимо', 'hint' => 'Запрещает элемент к отображению'),
		'enabled' => array('type' => 'boolean', 'title' => 'Включено', 'value' => true, 'hint' => 'Если узел отключен, то он не обрабатывается'),
		'enableRules' => array('type' => 'rules', 'title' => 'Условия активации', 'hint' => 'Данная настройка позволяет определить алгоритм срабатывания узла. Если при вычислении набора условий все они оказываются истинными, то узел активируется'),

		// City link
		// ---------
		'cities' => array('type' => 'list', 'title' => 'Города для отображения', 'format' => array(
			'type' => 'object', 'class' => 'geo-city'
		)),

		'order' => array('type' => 'number', 'title' => 'Порядок вывода', 'hint' => 'Порядок активации позволяет определить очередоность активации узлов одного уровня. Это важно, так как определяет порядок внесения изменений в вывод'),

		// Data
		// ----
		'data' => array('type' => 'record', 'title' => 'Данные'),

		// Processing
		// ----------
		'options' => array('type' => 'record', 'title' => 'Опции', 'format' => array(

			// Page options
			// -----------
			'page' => array('type' => 'record', 'title' => 'Управление страницей', 'format' => array(
				'title' => array('type' => 'text', 'title' => 'Название страницы'),
				'template' => array('type' => 'component', 'title' => 'Шаблон страницы', 'componentType' => 'template'),
				'theme' => array(),
				'htmlClasses' => array('type' => 'list', 'title' => 'Классы HTML', 'format' => array('type' => 'text')),
			)),

			// Menu
			// ----
			'menu' => array('type' => 'record', 'title' => 'Работа с меню', 'format' => array(
				'enable' => array('type' => 'boolean', 'title' => 'Добавлять в меню'),
				'title' => array('type' => 'text', 'title' => 'Заголовок в меню'),
				'order' => array('type' => 'number', 'title' => 'Приоритет'),
				'menuList' => array('type' => 'list', 'title' => 'Список меню', 'format' => array('type' => 'text')),
				'ignoreChildren' => array('type' => 'boolean', 'title' => 'Не добавлять потомков'),
				'icon' => array('type' => 'media', 'title' => 'Иконка в меню', 'folderPath' => array('Иконки меню')),
			)),

			// Cache
			// -----
			'cache' => array('type' => 'record', 'title' => 'Кэширование', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Включить кэширование'),
				'expiration' => array('type' => 'number', 'title' => 'Время жизни кэша', 'units' => 'секунд'),
				'options' => array('type' => 'multiselect', 'title' => 'Опции кэширования', 'values' => array(
					'user' => 'По пользователю',
					'uri' => 'По ссылке',
					'host' => 'По хосту',
					'request' => 'По параметрам запроса'
				))
			)),

            'htmlClasses' => array('title' => 'Классы HTML', 'type' => 'list', 'format' => array('type' => 'text'), 'hint' => 'Классы, которые будут добавлены в тэг виджета при выводе. Это позволяет изменять оформление данного виджета, переопределяя его стили'),

		)),
		// SEO options
		// -----------
		'seo' => array('type' => 'record', 'title' => 'SEO', 'format' => array(
			'title' => array('type' => 'text', 'title' => 'Заголовок'),
			'alias' => array('type' => 'text', 'title' => 'Абсолютный URL'), 
			'keywordsMode' => array('type' => 'select', 'title' => 'Режим обновления ключевых слов', 'allowEmpty' => true,   'values' => array(
				'append' => 'Добавлять к текущим',
				'overwrite' => 'Замещать текущие',
				'clear' => 'Очистить все'
			)),
			'keywords' => array('type' => 'list', 'title' => 'Ключевые слова', 'format' => array(
				'type' => 'text'
			)),
			'description' => array('type' => 'text', 'title' => 'Описание страницы', 'input' => 'textarea')
		)),

		// Widgets
		// -------
		'widgets' => array('type' => 'list', 'title' => 'Виджеты узла', 'format' => array(
			'type' => 'record', 'format' => array(
				'id' => array('type' => 'object', 'title' => 'Виджет', 'class' => 'widget'),
				'args' => array('type' => 'record', 'title' => 'Аргументы'),
				'options' => array('type' => 'record', 'title' => 'Опции вывода')
			)
		))

	);

	public static $selectorJSClass = 'nodesManager';

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'or', 'rules' => array(
			array('type' => 'userRole', 'role' => 'administrator'),
			array('type' => 'userRole', 'role' => 'content-manager'),
			array('type' => 'userRole', 'role' => 'seo')
		))),
		'read' => array(array('type' => 'true'))
	);

	// Indexes
	// -------
	public static $classIndexes = array(
		array(array('title' => 'text'))
	);

	// Identity is a path
	// -------------------
	public function getIdentity() {
		$node = \Node::getNodeObject($this);
    
    if (method_exists($node,'getIdentity')) return $node->getIdentity();
    
		return $node->getURI();
	}

	// Editor controller
	// -----------------
	public $editorJSController = 'NodeEditorController';

	// Create child action
	// -------------------
	public function actionCreateChild() {
		\Events::send('addScript', 'new Apps.start("objectEditor", { class : "'.static::$component['id'].'", data : { parent : "'.$this->_id.'"}});');
		return true;
	}

	// Open current resource as folder inside manager
	// ---------------------------------------------
	public function actionOpenInManager() {
		$scriptArgs = array('parent' => $this->_id);
		\Events::send('addScript', 'new Apps.start("nodesManager", '.json_encode($scriptArgs, 1).');');
		return true;
	}

	// Get class properties
	// --------------------
	public function getClassProperties() {

		// Get properties
		// --------------
		$properties = parent::getClassProperties();
		if (empty($properties)) return;


		// Get node type
		// --------------
		$nodeTypeComponent = \Core::getComponent('node', $this->type);

		// Append node properties
		// ----------------------
		if (!empty($nodeTypeComponent)) {
			$nodeProperties = $nodeTypeComponent::getNodeDataFormat();
			if (!empty($nodeProperties)) {
				$properties['data'] = array('type' => 'record', 'title' => 'Данные', 'format' => $nodeProperties);
			}
		}
		
		$themesModule = \Core::getModule('themes');
		$properties['options']['format']['page']['format']['theme'] = array('type' => 'select', 'title' => 'Тема оформления', 'values' => $themesModule->getThemesList(), 'allowEmpty' => true);

		return $properties;
	}

	// Get editor structure
	// ---------------------
	public function getEditorStructure() {


		// Get parent structure
		// --------------------
		$structure = parent::getEditorStructure();

		// Get type's properties
		// ---------------------
		$typeProperties = array();

		// Add properties to list
		// ----------------------
		if (!empty($typePropertiesList)) {
			foreach($typePropertiesList as $propertyID => $property) {
				$typeProperties[] = $propertyID;
			}
		}

		// Get this type properties
		// ------------------------
		$nodePropertiesList = array();
		if (!empty($this->type)) {

			// Get node type
			// --------------
			$nodeTypeComponent = \Core::getComponent('node', $this->type);
			if (!empty($nodeTypeComponent)) {

				$nodeProperties = $nodeTypeComponent::getNodeDataFormat();
				$nodeDataStructure = $nodeTypeComponent::getNodeDataStructure();

				if (!empty($nodeProperties)) {
					// Append property
					// ---------------
					foreach($nodeProperties as $nodePropertyID => $nodeProperty) {
						$nodePropertiesList[] = $nodePropertyID;
					}
				}

			}

		}

		// Append node args
		// ----------------
		$structure[0]['elements']['main'] = array(
			'type' => 'tab',
			'title' => 'Основное',
			'elements' => array(

				// Main
				// ----
				'main' => array('type' => 'block', 'title' => 'Основные', 'elements' => array(
					array('type' => 'form', 'properties' => array('title', 'path', 'type', 'parent', 'order', 'isSystem'))
				)),

				// Data
				// ----
				'data' => array('type' => 'block', 'title' => 'Данные', 'elements' => array(
					'dataForm' => array('type' => 'form', 'property' => 'data')
				)),

				// Visibility settings
				// -------------------
				'visibility' => array('type' => 'block', 'title' => 'Видимость', 'closed' => true, 'elements' => array(
					array('type' => 'form', 'properties' => array('hidden', 'enabled', 'enableRules'))
				)),
			)
		);

		// Append display
		// --------------
		$structure[0]['elements']['options'] = array(
			'type' => 'tab',
			'title' => 'Опции',
			'elements' => array(
				'displayForm' => array('type' => 'form', 'property' => 'options')
			)
		);

		// Append widgets
		// --------------
		$structure[0]['elements']['widgets'] = array(
			'type' => 'tab',
			'title' => 'Виджеты',
			'elements' => array(
				'displayForm' => array('type' => 'controller', 'property' => 'widgets', 'controllerClass' => 'SiteNodeWidgetsList')
			)
		);

		// Append SEO
		// ----------
		$structure[0]['elements']['seo'] = array(
			'type' => 'tab',
			'title' => 'SEO',
			'elements' => array(
				'displayForm' => array('type' => 'form', 'property' => 'seo')
			)
		);


		// Return modified editor structure
		// --------------------------------
		return $structure;

	}

	// Remove node
	// -----------
	public function delete() {

		// Read subnodes
		// -------------
		$subnodes = static::find(array('query' => array('parent' => $this->_id)));

		// Remove them
		// -----------
		if (!empty($subnodes)) {
			foreach($subnodes as $subnode) $subnode->delete();
		}

		// And this one too
		// ----------------
		parent::delete();

	}

	// Check if subnode exsists
	// ------------------------
	public function hasSubnode($nodeID) {

		// Read all subnodes
		// ------------------
		$subnodes = static::find(array('query' => array('parent' => $this->_id)));

		// Check if given node is subnode
		// ------------------------------
		if (!empty($subnodes))
			foreach($subnodes as $subnode) {
				if ($subnode->_id == $nodeID) return true;
				if ($subnode->hasSubnode($nodeID) == true) return true;
			}

		// No any matches found
		// --------------------
		return false;
	}

	// Drop node
	// ---------
	public function actionDropNode($data) {

		// Not possible
		// ------------
		if(empty($data['id'])) return;

		// Get node
		// --------
		$nodeClass = \Core::getClass('node');
		$nodeObject = $nodeClass::findPK($data['id']);
		if (empty($nodeObject)) return;

		// Check node intersections
		// -----------------------
		if ($nodeObject->hasSubnode($this->_id)) {
			return;
		}

		// Set parent
		// ----------
		$nodeObject->parent = $this->_id;
		$nodeObject->save();

		// OK
		// --
		return true;
	}


}
