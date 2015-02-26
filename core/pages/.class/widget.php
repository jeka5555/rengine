<?php

// Класс. Виджет
// --------------------------
namespace Objects;

class Widget extends \ObjectClass {

	// Информация компонента
	// ---------------------
	public static $component = array(
		'type' => 'class',
		'id' => 'widget',
		'title' => 'Виджет',
		'editable' => true
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'or', 'rules' => array(
			array('type' => 'userRole', 'role' => 'administrator'),
			array('type' => 'userRole', 'role' => 'developer')
		))),
		'read' => array(array('type' => 'true'))
	);

	// Class actions
	// -------------
	public static $classActions = array(
		'save' => array('title' => 'Сохранить', 'classAction' => true),
		'disable' => array('title' => 'Отключить', 'bulkAction' => true),
		'enable' => array('title' => 'Включить', 'bulkAction' => true)
	);

	// Editor controller
	// -----------------
	public $editorJSController = 'WidgetEditorController';

	// Class properties
	// ----------------
	public static $classProperties = array(

		// Main properties
		// ---------------
		'type' => array('type' => 'component', 'title' => 'Тип виджета', 'componentType' => 'widget', 'listing' => true, 'sortable' => true),
		'title' => array('type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true),
		'description' => array('type' => 'text', 'title' => 'Описание', 'input' => 'textarea'),


		// Arguments of widget
		// -------------------
		'args' => array('type' => 'record', 'title' => 'Аргументы виджета', 'format' => array(
		)),

		// Output options
		// --------------
		'options' => array('type' => 'record', 'title' => 'Опции вывода', 'format' => array(

			// Disabled
			// ---------
			'disabled' => array('type' => 'boolean', 'title' => 'Отключено', 'listing' => true, 'sortable' => true),
			'visibility' => array('type' => 'rules', 'title' => 'Правила видимости'),

			'block' => array('type' => 'text', 'title' => 'Блок для вывода', 'listing' => true, 'sortable' => true, 'hint' => 'Блок в шаблоне, в который будет осуществляться вывод виджета'),
			'order' => array('type' => 'number', 'title' => 'Порядок в блоке', 'listing' => true, 'sortable' => true, 'hint' => 'Порядок вывода'),

			// HTML
			// ----
			'htmlTag' => array('type' => 'select', 'title' => 'HTML-тэг', 'values' => array('div' => 'div', 'span' => 'span', 'section' => 'section'), 'allowEmpty' => true),
			'htmlID' => array('type' => 'text', 'title' => 'Идентификатор HTML'),
			'htmlClasses' => array('title' => 'Классы HTML', 'type' => 'list', 'format' => array('type' => 'text'), 'hint' => 'Классы, которые будут добавлены в тэг виджета при выводе. Это позволяет изменять оформление данного виджета, переопределяя его стили'),

			// Additional
			// ----------
			'template' => array('title' => 'Шаблон для вывода', 'type' => 'text'),
			'wrappers' => array('title' => 'Внешние элементы обертки', 'type' => 'list', 'format' => array('type' => 'text'), 'hint' => 'Если значение не пустое, то будут созданы дополнительные контейнеры вокруг виджета, которые бдут иметь класса'),
			'tag' => array('title' => 'Тэг обертки', 'type' => 'select', 'allowEmpty' => true, 'values' => array('div' => 'DIV', 'span' => 'SPAN')),

			// Cache
			// -----
			'cache' => array('title' => 'Настройки кэширования', 'type' => 'record', 'format' => array(
				'enabled' => array('type' => 'boolean', 'title' => 'Включить кэш'),
				'expire' => array('type' => 'number', 'title' => 'Время жизни'),
				'options' => array('type' => 'record', 'title' => 'Флаги валидации', 'mode' => 'full', 'format' => array(
					'id' => array('type' => 'text', 'title' => 'Идентификатор объекта', 'allowNull' => true),
					'uri' => array('type' => 'boolean', 'title' => 'URI страницы'),
					'user' => array('type' => 'boolean', 'title' => 'Пользователь')
				))
			)),

			// Content
			// ----------
			'contentBefore' => array('title' => 'Контент до', 'type' => 'textarea', 'isHTML' => true, 'hint' => 'По умолчанию контент данного поля выводится в начале виджета'),
			'contentAfter' => array('title' => 'Контент после', 'type' => 'textarea', 'isHTML' => true, 'hint' => 'По умолчанию контент данного поля выводится в конце виджета'),

		))
	);

	// Widget args format
	// ------------------
	public static $widgetArgsFormat = array();

	// Get properties
	// --------------
	public function getClassProperties() {

		$properties = parent::getClassProperties();

		// Skip empty
		// ----------
		if (empty($this->type)) return $properties;

		// Get args
		// --------
		$args = \Core::getModule('widgets')->actionGetWidgetArgsFormat($this->type);
		$properties['args']['format'] = $args;

		// Return result
		// -------------
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
		if (!empty($typePropertiesList))
			foreach($typePropertiesList as $propertyID => $property) {
				$typeProperties[] = $propertyID;
			}

		// Append args data
		// ----------------
		$structure[0]['elements']['args'] = array(
			'type' => 'tab',
			'title' => 'Аргументы виджета',
			'elements' => array(
				'argsForm' => array('type' => 'form', 'property' => 'args')
			)
		);

		// Append display data
		// -------------------
		$structure[0]['elements']['display'] = array(
			'type' => 'tab',
			'title' => 'Отображение',
			'elements' => array(
				'seoForm' => array('type' => 'form', 'property' => 'options')
			)
		);


		// Return modified editor structure
		// --------------------------------
		return $structure;

		// Get arguments
		// -------------
		$argsFormat = $this->widget->getWidgetArgsFormat();

	}

	// Get format
	// ----------
	public function getManagerTableFormat() {

		// Get old format
		// --------------
		$format = parent::getManagerTableFormat();

		// Append
		// ------
		$format[] = array('id' => 'description', 'title' => 'Описание');
		$format[] = array('id' => 'block', 'title' => 'Блок');
		$format[] = array('id' => 'order', 'title' => 'Порядок');
		$format[] = array('id' => 'disabled', 'title' => 'Отключено');

		// Return
		// ------
		return $format;
	}

	// Save
	// ----
	public function save($args = array()) {
		unset($this->properties['widget']);
		parent::save();
	}

	public function getManagerTableData() {

		$data = parent::getManagerTableData();

		$data['description'] = @ $this->options['description'];
		$data['block'] = @ $this->options['block'];
		$data['order'] = @ $this->options['order'];
		$data['disabled'] = @ $this->disabled;

		return $data;
	}

	// Create new object of required class
	// -----------------------------------
	public static function getInstance($data = array()) {

		// Create widget placeholder
		// ------------------------
		$widgetProxy = new self();
		$widgetProxy->properties = $data;
		if (empty($data['_id'])) $widgetProxy->properties['_id'] = (string) new \MongoID();

		// Detect widget type
		// ------------------
		$widgetType = @ $data['type'];

		// Rewrite widget
		// --------------
		if (!empty($data['options']['widget'])) {
			if (\Core::getComponent('widget', @ $data['options']['widget'])) {
				$widgetType = @ $data['options']['widget'];
			}
		}

		// Try to get class
		// ----------------
		$widgetClass = \Core::getComponent('widget', $widgetType);
		if (empty($widgetClass)) {
			$widgetClass = '\Widget';
		}

		// Create widget
		// -------------
		$widgetProxy->widget = $widgetClass::getInstance($data);

		// Add ID
		// -------
		if (!empty($widgetProxy->widget)) $widgetProxy->widget->id = @ $data['_id'];

		// Return object
		// -------------
		return $widgetProxy;
	}

	// Get widget's content
	// --------------------
	public function get($variable = null) {

		// Parent
		// ------
		if (!empty($variable)) return parent::get($variable);

		// Or render widget
		// ----------------
		if (empty($this->widget)) return null;
		return $this->widget->get();
	}

	// Prepare widget to render
	// ------------------------
	public function preRender() {
		if (empty($this->widget)) return null;
		return $this->widget->preRender();
	}


}
