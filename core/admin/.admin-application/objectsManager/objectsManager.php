<?php

namespace Core\Admin\AdminApplications;

// Класс приложения для менеджера объектов
// ------------------------------------
class ObjectsManager extends \Core\Admin\Components\AdminApplication {

	// Описание компонента
	// -------------------
	public static $component = array(
		'type' => 'admin-application',
		'id' => 'objectsManager',
		'addOnToolbar' => true,
		'title' => 'Объекты',
		'icon' => '/core/admin/.admin-application/objectsManager/icon.png',
		'access' => array(
			array('type' => 'or', 'rules' => array(
				array('type' => 'userRole', 'role' => 'super'),
				array('type' => 'userRole', 'role' => 'administrator'),
				array('type' => 'userRole', 'role' => 'content-manager')
			))
		)
	);

	// Run application
	// ---------------
	public function commandInit($args = array()) {

		// Result
		// ------
		$result = array();
		$resultClasses = array();

		// Собираем все доступные типы классов
		// ---------------------------
		$classes = @ \Extension::$ext['class'];

		// Если есть классы объектов, строим из них список
		// -----------------------------------------------
		if (!empty($classes)) {
			foreach ($classes as $classIndex => $classData) {

				// Get class
				// ---------
				$class = \Core::getComponent('class', $classData['id']);
				if (@ $class::$component['editable'] != true) continue;

				// Check access
				// ------------
				$access = $class::checkClassAccess('editClass');

				// Get class title
				// ---------------
				$classTitle = first_var(@ $class::$component['title'], @ $class::$component['id'], 'Не определено');

				// Add count
				// ---------
				$classTitle .= ' ('.$class::find(array('count' => true)).')';

				// Если есть права и class редактируемый
				// ---------------------------
				if (@$args['selectMode'] == true || (@$class['editable'] == true and $access))

					// Если указаны classes
					// ---------------------------
					if(!empty($args['classes'])) {

						// Проверяем входит ли данный class в список указанных
						// ---------------------------
						if(in_array($class::$component['id'], $args['classes']))
							$resultClasses[$class::$component['id']] = $classTitle;
						}

						// Помещаем информацию
						// --------------------
						else {
							$resultClasses[$class::$component['id']] = $classTitle;
						}
			}
		}

		// Sort data
		// ---------
		natsort($resultClasses);
		$result['classes'] = $resultClasses;

		// Если передан класс, отдаем его поля
		// -----------------------------------
		if (!empty($args['classID'])) {

			// Формат полей текущего класса
			// ----------------------------
			$result['classFormat'] = self::commandGetClassFormat(array('classID' => $args['classID']));

			// @todo  Данные для таблицы объектов
			// ----------------------------------
		}


		return $result;

	}


	// Get objects table
	// -----------------
	public static function commandGetObjectsTable($args = array()) {

		$query = array();

		// Get class
		// ---------
		if (empty($args['class'])) return false;
		$objectClass = \Core::getComponent('class', $args['class']);
		if (empty($objectClass)) return;

		// Sort options
		// ------------
		$sort = first_var(@ $args['sort'], array('@createTime' => -1));

		// Search filters
		// ---------------
		if (!empty($args['filters'])) {
			$query = array_merge($query, $objectClass::getSearchQuery($args['filters']));
		}

		// Read properties
		// ---------------
		$properties = $objectClass::getInstance()->getClassProperties();

		// Get table format
		// ----------------
		$tableFormat = $objectClass::getInstance()->getManagerTableFormat();

		// Count objects and pages
		// -----------------------
		$objectsCount = $objectClass::safeFind(array(
			'query' => $query, 'count' => true,
			'accessMode' => 'edit'
		));

		// Read objects of current page
		// ----------------------------
		$pagesCount = ceil($objectsCount / 20);
		if (!empty($args['page'])) $skip = $args['page'] * 20; else $skip = 0;

		// Read objects
		// ------------
		$objects = $objectClass::safeFind(array(
			'query' => $query,
			'limit' => 20,
			'skip' => $skip,
			'sort' => $sort,
			'accessMode' => 'edit'
		));

		// Get object data
		// ---------------
		$tableData = array();
		foreach($objects as $item) {
			$tableData[] = $item->getManagerTableData();
		}

		// Build class actions
		// -------------------
		$classActions = $objectClass::getClassActionsList();

		// Get class fields which are searchable
		// -------------------------------------
		$classSearchFormat = array();

		if (!empty($properties)) {
			foreach($properties as $fieldID => $field) {

				// Require only for searchable fields
				// ----------------------------------
				if (@ $field['searchable'] == true) {
					$classSearchFormat[$fieldID] = $field;
				}
			}
		}

		// Default search format
		// ----------------------
		if (empty($classSearchFormat)) $classSearchFormat = array(
			array('id' => '@text', 'title' => 'По текстовым полям', 'type' => 'text')
		);

		// Return data
		// -----------
		return array(
			'objectsCount' => $objectsCount,
			'tableFormat' => $tableFormat,
			'classActions' => $classActions,
			'classSearchFormat' => $classSearchFormat,
			'classData' => array(
				'id' => @ $objectClass::$component['id'],
				'title' => first_var(@ $objectClass::$component['title'], @ $objectClass::$component['id'])
			),
			'tableData' => $tableData,
			'pagesCount' => $pagesCount
		);
	}

}
