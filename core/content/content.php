<?php

namespace Core\Modules;

use Core\Components\ObjectEditor;

class Content extends \Module {

	public static $component = array(
		'id' => 'content',
		'title' => 'Вывод контента',
		'description' => 'Модуль для визуализации содержимого, отредактированного при помощи редактора контента',
		'hasSettings' => true
	);


	// Format
	// ------
	public static $componentSettingsFormat = array(
		'useContentNodes' => array('type' => 'boolean', 'title' => 'Использовать узлы для вывода контента'),
		'contentBlock' => array('type' => 'text', 'title' => 'Блок для вывода контента')
	);


	// Settings
	// --------
	public static $settings = array(
		'useContentNodes' => true,
		'contentBlock' => 'content'
	);


	// Render content that contains some content blocks
	// ------------------------------------------------
	public function actionRenderContent($blocks = array()) {

		// Skip non arrays
		// ---------------
		if (!is_array($blocks)) return;

		// Content will be collected here
		// ------------------------------
		$content = '';

		// Render blocks
		// -------------
		foreach($blocks as $block) {
			$content .= $this->actionRenderBlock($block);
		}



		return $content;

	}

	// Render single content block
	// ---------------------------
	public function actionRenderBlock($args = array()) {

		// Get type of block
		// -----------------
		$blockType = @ $args['type'];
		if (empty($blockType)) return;

		// Get block component
		// -------------------
		$blockComponent = \Core::getComponent('content-block', $blockType);
		if (empty($blockComponent)) return;

		// Create a block instance and init it
		// -----------------------------------
		$block = $blockComponent::getInstance($args);

		// Render block
		// ------------
		$content = $block->render();

		return $content;

	}

	// Get editor format
	// -----------------
	public function actionGetWidgetEditor($args = array()) {

		// Build result
		// ------------
		$structure = array(

			// All properties are here
			// -----------------------
			'main' => array('type' => 'tab', 'title' => 'Основные свойства', 'order' => 0, 'elements' => array(

				// Basic object properties
				// -----------------------
				array('type' => 'block', 'title' => 'Свойства объекта', 'elements' => array(
					array('type' => 'form', 'properties' => array('type'))
				))
			)),
		);

		$structure = array(array('type' => 'tabs', 'elements' => $structure));

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


		$argsFormat = array();
		if (!empty($args['type']))$argsFormat = \Core::getModule('widgets')->actionGetWidgetArgsFormat($args['type']);


		// Return modified editor structure
		// --------------------------------
		return array(
			'structure' => $structure,
			'properties' => array(

				'type' => array('type' => 'component', 'title' => 'Тип виджета', 'componentType' => 'widget'),
				'args' => array('type' => 'record', 'title' => 'Опции виджета', 'format' => $argsFormat),
				'options' => array('type' => 'record', 'title' => 'Опции отображения', 'format' => array(
					// Disabled
					// ---------
					'disabled' => array('type' => 'boolean', 'title' => 'Отключено', 'listing' => true, 'sortable' => true),
					'visibility' => array('type' => 'rules', 'title' => 'Правила видимости'),

					// HTML
					// ----
					'htmlTag' => array('type' => 'select', 'title' => 'HTML-тэг', 'values' => array('div' => 'div', 'span' => 'span', 'section' => 'section'), 'allowEmpty' => true),
					'htmlID' => array('type' => 'text', 'title' => 'Идентификатор HTML'),
					'htmlClasses' => array('title' => 'Классы HTML', 'type' => 'list', 'format' => array('type' => 'text'), 'hint' => 'Классы, которые будут добавлены в тэг виджета при выводе. Это позволяет изменять оформление данного виджета, переопределяя его стили'),

					// Additional
					// ----------
					'template' => array('title' => 'Шаблон для вывода', 'type' => 'text'),
					'wrappers' => array('title' => 'Внешние элементы обертки', 'type' => 'text', 'hint' => 'Если значение не пустое, то будут созданы дополнительные контейнеры вокруг виджета, которые бдут иметь класса'),
					'tag' => array('title' => 'Тэг обертки', 'type' => 'select', 'allowEmpty' => true, 'values' => array('div' => 'DIV', 'span' => 'SPAN')),

					// Content
					// ----------
					'contentBefore' => array('title' => 'Контент до', 'type' => 'textarea', 'isHTML' => true, 'hint' => 'По умолчанию контент данного поля выводится в начале виджета'),
					'contentAfter' => array('title' => 'Контент после', 'type' => 'textarea', 'isHTML' => true, 'hint' => 'По умолчанию контент данного поля выводится в конце виджета'),

				))
			)
		);

	}
}
