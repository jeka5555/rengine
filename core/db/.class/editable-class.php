<?php

class EditableClass extends SafeClass {

	// Editor format
	// -------------
	public static $classProperties = array();


	// Get properties format
	// ---------------------
	public function getClassProperties() {

		// Generic data
		// ------------
		$properties = array(
			'_id' => array('title' => 'Идентификатор объекта', 'type' => 'generic',
				'access' => array(array('type' => 'or', 'rules' => array(array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super'))) )
			)
		);

		// Merge class properies
		// ---------------------
		$classProperties =  static::$classProperties;
		if (!empty($classProperties)) $properties = array_merge($properties, $classProperties);

		// Add admin properties
		// --------------------
		if ($this->checkAccess('control')) {

			// Add editable ID
			// ---------------
			$properties['_id'] = array('title' => 'Идентификатор объекта', 'type' => 'text',
				'hint' => 'Системный идентификатор объекта. Каждый объект должен иметь уникальный идентификатор',
				'access' => array(array('type' => 'or', 'rules' => array(array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super'))) )
			);

			// Object's owner
			// --------------
			$properties['@owner'] = array('title' => 'Владелец объекта', 'type' => 'object', 'class' => 'user',
				'hint' => 'Пользователь, который является владельцем объекта, и имеет административные права на него. По умолчанию владельцем объекта является его автор',
				'access' => array(array('type' => 'or', 'rules' => array(array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super')	)) )
			);

			// Add editable ID
			// ---------------
			$properties['@tags'] = array('title' => 'Тэги', 'type' => 'list',
				'hint' => 'Тэги объекта для удобного поиска',
				'format' => array('type' => 'text'),
				'access' => array(array('type' => 'or', 'rules' => array(array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super'))) )
			);

			// Time of creation
			// ----------------
			$properties['@createTime'] = array('title' => 'Время создания', 'type' => 'datetime',
				'access' => array(array('type' => 'or', 'rules' => array( array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super') )) )
			);

			// Object description
			// ------------------
			$properties['@description'] = array('title' => 'Описание объекта', 'type' => 'text', 'input' => 'textarea',
				'hint' => 'Необязательное описание, которое помогает идентифицировать объект в списике поъожих объектов при просмотре',
				'access' => array( array('type' => 'or', 'rules' => array( array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super') )) )
			);

			// Object access
			// -------------
			$properties['@access'] = array('title' => 'Права на доступ', 'type' => 'accessControl',
				'hint' => 'Данное свойство позволяет задавать кто из пользователей может осуществлять операции с текущим объектом. Имеются два режима - просмотр и редактирование.',
				'access' => array(array('type' => 'or', 'rules' => array( array('type' => 'userRole', 'role' => 'administrator'), array('type' => 'userRole', 'role' => 'super') )) 	)
			);

		}

		// Return properties
		// -----------------
		return $properties;

	}

	// Get editor blocks
	// -----------------
	public function getEditorStructure() {

		// List of all properties
		// ----------------------
		$properties = $this->getClassProperties();
		$propertiesList = array();

		foreach($properties as $propertyID => & $property) {
			$propertiesList[] = $propertyID;
		}

		// Build result
		// ------------
		$result = array(

			// All properties are here
			// -----------------------
			'main' => array('type' => 'tab', 'title' => 'Основные свойства', 'order' => 0, 'elements' => array(

				// Basic object properties
				// -----------------------
				array('type' => 'block', 'title' => 'Свойства объекта', 'elements' => array(
					array('type' => 'form', 'properties' => $propertiesList)
				))
			)),

			// Admin tab and form
			// ------------------
			'admin' => array('type' => 'tab', 'title' => 'Администрирование', 'order' => 200, 'elements' => array(
				'adminForm' => array('type' => 'form', 'properties' => array('_id', '@description', '@owner', '@access', '@createTime', '@tags'))
			))
		);

		return array(array('type' => 'tabs', 'elements' => $result));
	}

    public function fieldFormatting($field, $value = '') {

        // Форматирование вывода в сотвествии с данными
        // --------------------------------------------
        switch(@ $field['type']) {

            // Для поля с выбором
            // ------------------
            case "select":
                if (!empty($field['values']) && array_key_exists($value, $field['values'])) {
                    $value = $field['values'][$value];
                }
                else $value = null;
                break;

            // Текстовое поле
            // --------------
            case "text":
                $value = \DataView::get('text', $value, array('maxChars' => 50));
                break;

            // Булево поле
            // -----------
            case "boolean":
                if ($value == true) $value = 'да'; else $value = 'нет';
                break;

            // Дата и время
            // -----------
            case "datetime":
                $value = \DataView::get("datetime", $value, array('format' => @$field['format']));
                break;

            // Объектное поле
            // --------------
            case "object":

                if (!empty($field['class'])) {
                    if ($class = \Core::getComponent('class', $field['class'])) {
                        $object = call_user_func(array($class, 'findPK'), $value);
                        if (!empty($object)) $value = $object->getIdentityTitle();
                        else $value = '-';
                    }
                }
                break;

            case "list":
                if (!empty($value) and is_array($value)) {
                    $valueArray = $value;
                    $value = array();
                    foreach ($valueArray as $item) {
                        $value[] = $this->fieldFormatting($field['format'], $item);
                    }
                    $value = implode(', ', $value);
                }
                break;

        }

        return $value;

    }


	// Get data formatted for editor
	// -----------------------------
	public function getManagerTableData() {

		$result = array();


		$format = $this->getManagerTableFormat();
		if (!empty($format)) {

			// Проверяем все поля
			// ------------------
			foreach ($format as $fieldID => $field) {

				// Get column id
				// -------------
				if (empty($field['id'])) $id = $fieldID;
				else $id = $field['id'];

                // Get property
                // ------------
                $value = @$this->properties[$id];

                $value = $this->fieldFormatting($field, $value);

				// Append value
				// ------------
				$result[$id] = $value;
			}
		}
		// Add id
		// ------
		$result['_id'] = @$this->_id;

		return $result;

	}


	// Get format of object's manager table
	// ------------------------------------
	public function getManagerTableFormat() {

		// Format and fields
		// -----------------
		$format = array();

		// Itterate all
		// ------------
		$properties = $this->getClassProperties();
		if (!empty($properties)) {

			foreach($properties as $fieldID => $field) {

				// Skip not listed
				// ---------------
				if (@ $field['listing'] != true) continue;

				// Skip if can't access
				// --------------------
				if (!$this->checkPropertyAccess($fieldID)) continue;

				// Sortable flag
				// -------------
				$sortable = (@ $field['sortable'] == true);

				// Add title
				// ---------
				if (empty($field['title'])) $field['title'] = $fieldID;

				// Add field with correct ID
				// -------------------------
				$field['id'] = $fieldID;
				$format[] = $field;

			}
		}

		// Возврат результата
		// ------------------
		return $format;

	}


	// Init component
	// --------------
	public static function initComponent() {

		// Parent's initializer
		// --------------------
		parent::initComponent();

		// Get parent class
		// ----------------
		$parentClass = get_parent_class(get_called_class());

		// Any actions?
		// ------------
		if (!empty($parentClass)) {
			if (!empty($parentClass::$classProperties)) {
				static::$classProperties = array_merge($parentClass::$classProperties + static::$classProperties);
			}
		}
	}
}
