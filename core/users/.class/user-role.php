<?php

namespace Users\Classes;

class UserRole extends \ObjectClass {

	// Component
	// ---------
	public static $component = array(
		'type' => 'class',
		'id' => 'userRole',
		'title' => 'Роль пользователя',
		'editable' => true
	);

	// Class access rules
	// ------------------
	public static $classAccessRules = array(
		'default' => array(array('type' => 'userRole', 'role' => 'administrator')),
		'get' => array(array('type' => 'true')),
	);

	// Class properties
	// ----------------
	public static $classProperties = array(
		'id' => array('type' => 'text', 'title' => 'Идентификатор', 'listing' => true, 'sortable' => true),
		'title' => array( 'type' => 'text', 'title' => 'Название', 'listing' => true, 'sortable' => true),
		'description' => array('type' => 'text', 'title' => 'Описание', 'input' => 'textarea'),
		'permissions' => array('type' => 'list', 'title' => 'Список привелегий', 'format' => array('type' => 'object', 'class' => 'userPermission')),
        'roleGroup' => array('type' => 'object', 'class' => 'roleGroup', 'title' => 'Группа роли', 'listing' => true, 'sortable' => true),
        'hide' => array('type' => 'boolean', 'title' => 'Скрыть'),
        'subordinateRoles' => array('type' => 'list', 'title' => 'Роли подчиненных', 'format' => array(
            'type' => 'object', 'class' => 'userRole', 'title' => 'Роль'
        )),
	);

    public static $roles = array();
    public static $rolesTitle = array();

	// Get identity title
	// ------------------
	public function getIdentityTitle() {
		return first_var(@ $this->title, @ $this->id);
	}

    public static function initComponent() {
        // Формируем массив стутусов с Идентификаторами в качестве ключей и id в качестве значения

        $className = __CLASS__;
        $items = $className::find();

        foreach($items as $item) {
            $itemsArray[$item->id] = $item->_id;
            $itemsArrayTitle[$item->_id] = $item->title;
        }

        self::$roles = @$itemsArray;
        self::$rolesTitle = @$itemsArrayTitle;

    }

}