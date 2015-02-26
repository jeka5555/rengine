<?php

class SafeClass extends StorableClass {


	// Safe set
	// --------
	public function safeSet($args = array(), $value = null) {

		// Simple property
		// ---------------
		if (is_string($args)) {
			if ($this->checkPropertyAccess($args)) {
				$this->set($args, $value);
			}
		}

		// Array of properties
		// ---------------
		else if (is_array($args)) {
			foreach($args as $variable => $value) {
				if ($this->checkPropertyAccess($variable)) {
					$this->set($variable, $value);
				}
			}
		}
	}



	// Delete object
	// -------------
	public function safeDelete() {

		// Log
		// ----------------
		\Logs::log(array('action' => 'delete','class' => @ static::$component['id'], 'object_id' => @$this->_id, 'identity' => $this->getIdentity()), 'objects');

		// Check access
		// ------------
		if (!$this->checkAccess('edit')) return false;

		// Return result
		// -------------
		return $this->delete();
	}


	// Save save
	// ---------
	public function safeSave() {

		// Log
		// ----------------
		\Logs::log(array('action' => 'save', 'class' => @ static::$component['id'], 'object_id' => @$this->_id, 'identity' => $this->getIdentity()), 'objects');

		// Check access
		// ------------
		if (!$this->checkAccess('edit')) return false;

		// Return result
		// -------------
		return $this->save();

	}


	// Safe read
	// ---------
	public static function safeFind($args = array()) {

		$accessMode = first_var(@$args['accessMode'], 'read');

		// Build query
		// ------------
		$query = first_var(@ $args['query'], array());
		$fields = first_var(@ $args['fields'], array());

		// Fetch
		// -----
		$connection = \DB::$connection;
		$collection = static::getCollectionName();
		$result = $connection->$collection->find($query, $fields);

		// Sort
		// ----
		if (!empty($args['sort'])) $result->sort($args['sort']);

		// Результат
		// ---------
		$newResult = array();

		// Перегоняем
		// ----------
		$skipCounter = first_var(@ $args['skip'], 0);
		$limitCounter = first_var(@ $args['limit'], 1000000000);


		// Перебор элементов в результат
		// -----------------------------
		if (!empty($result))
		foreach($result as $item) {

			// Создаем объект
			// --------------
			$object = static::getInstance($item);

			// Проверяем, есть ли правило для чтения
			// -------------------------------------
			if ($object->checkAccess($accessMode)) {

				// Пропуск
				// -------
				if ($skipCounter > 0) { $skipCounter--;	continue; }

				// Если ноль, то остановка
				// -----------------------
				if (--$limitCounter < 0) break;

				// Вставка в массив на выходе
				// --------------------------
				$newResult[] = $object;

			}
		}

		// Возврат количества
		// -------------------------------
		if (@ $args['count'] === true) return count($newResult);

	  // Преобразование результата
		// -------------------------
		$result = $newResult;

		// Первый элемент последовательности
		// ---------------------------------
		if (@ $args['first']) $result = @ current($result);

		return $result;

	}

	// Safe search with PK
	// -------------------
	public static function safeFindPK($key, $args = array()) {
		if (static::checkClassAccess('read')) return static::findPK($key, $args);
		return null;
	}


	// Безопасный поиск одного элемента
	// ---------------------
	public static function safeFindOne($args = array()) {
		if (static::checkClassAccess('read')) return static::findOne($args);
		return null;
	}



	// Безопасное удаление объектов
	// ----------------------------
	public static function safeFindAndDelete($args = array()) {

		// Запрос должен существовать
		// --------------------------
		if (empty($args['query'])) return false;

		// Удаляем каждый из объектов
		// --------------------------
		$objects = static::find(array('query' => $args['query']));
		if (!empty($objects)) {
			foreach($objects as $object) {
				if ($object->checkAccess('edit'))	$object->safeDelete();
			}
		}

		// Успех
		// -----
		return true;
	}



	public static function safeFindAndSave($data = array()) {


		// If id is set, try to load an object
		// -----------------------------------
		if (isset($data['_id'])) {

			// Read
			// ----
			$object = static::findOne(array('query' => array('_id' => $data['_id'])));

			// If object is correct
			// --------------------
			if (!empty($object)) {

				// Check access
				// ------------
				if (!$object->checkAccess('edit')) {

					// Log
					// ---
					\Logs::log(array('text' => 'Невозможно получить доступ на редактирвование', 'action' => 'editObjectAccess', 'class' => @ static::$component['id'], 'object_id' => @$object->_id, 'identity' => @ $object->getIdentity()), 'objects');
					return false;
				}

				// Log
				// ---
				\Logs::log(array('text' => 'Произведено изменени объекта', 'action' => 'save', 'class' => @ static::$component['id'], 'object_id' => @$object->_id, 'identity' => $object->getIdentity()), 'objects');

				// Modify and save
				// ---------------
				$object->safeSet($data);
				return $object->save();
			}

		}

		// Create new object
		// -----------------
		if (!static::checkClassAccess('edit')) {

			// Log
			// ---
			\Logs::log(array('text' => 'Невозможно получить доступ на редактирвование', 'action' => 'editObjectAccess', 'class' => @ static::$component['id']), 'objects');
			return false;
		}


		// Create object
		// -------------
		$object = static::getInstance();
		\Events::send('objectCreate', array('class' => static::$component['id']), array('client' => true));

		// Log
		// ---
		\Logs::log(array('text' => 'Создан новый объект', 'action' => 'create', 'class' => @ static::$component['id'], 'object_id' => @$object->_id,), 'objects');

		// Save
		// ----
		$object->safeSet($data);

		// Return result
		// -------------
		return $object->save();

	}





	// Безопасное обновление
	// ---------------------
	public static function safeFindAndUpdate($query = array(), $data = array()) {

		// Если нечего обновить, неудача
		// -----------------------------
		if (!is_array($data)) return false;

		$objects = static::safeFind(array('query' => $query));
		if (!empty($objects)) {

			// Обновляем объекты по одному
			foreach($objects as $object) {
				if (!$object->checkAccess('edit')) {

					// Сообщение в логе
					// ----------------
					\Logs::log(array(
						'action' => 'saveCheckAccess',
						'text' => 'Невозможно получить доступ к объекту',
						'class' => @ static::$component['id'],
						'object_id' => @$object->_id,
					), 'objects');

					return false;
				}


				// Производим сохранение
				// ---------------------
				$object->set($data);
				return $object->save();
			}
		}
		return true;

	}
}
