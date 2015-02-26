<?php

// Objects in database
// -------------------
class ObjectClass extends ActionableClass {

	// Base component data
	// -------------------
	public static $component = array(
		'type' => 'component',
		'id' => 'class',
		'title' => 'Класс объекта',
		'description' => 'Базовый класс'
	);


	// Basic class actions
	// -------------------
	public static $classActions = array(

		// Create
		// ------
		'create' => array(
			'title' => 'Создать',
			'classAction' => true
		),

		// Edit
		// ----
		'edit' => array(
			'title' => 'Редактировать'
		),

		// Delete
		// ------
		'delete' => array(
			'title' => 'Удалить',
			'requireConfirmation' => true,
			'confirmationMessage' => 'Вы действительно хотите удалить данный объект?',
			'bulkAction' => true),

		// Clone
		// -----
		'clone' => array(
			'title' => 'Копировать'
		)


	);


    // Возвращаем массив
    // ------------------------
    public static function getArray($selectedID = null) {

        $className = get_called_class();
        $items = $className::find();


        $itemsArray = array();

        foreach($items as $item) {
            $itemsArray[$item->_id] = $item->title;
        }

        return $itemsArray;

    }
	
	public function renderModePreview() {	
		$content = $this->title;		
		return $content;
	}

}