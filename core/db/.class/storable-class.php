<?php

class StorableClass extends AccessableClass {

	public static $databaseObjectsPool = array(); // Хранилище объектов

	// Get name of storable collection
	// -------------------------
	public static function getCollectionName() {
		$collectionName = static::$component['id'];
		return $collectionName;
	}

	// Make object from data
	// ---------------------
	public static function getInstance($data = array()) {


		if (empty($data['_id'])) $data['_id'] = (string) new MongoID();

		// Search in object's pool
		// -----------------------
		if (!empty($data['_id'])) {
		  if (array_key_exists($data['_id'], static::$databaseObjectsPool)) {
			  return static::$databaseObjectsPool[$data['_id']];
		  }
		}

		// Get class name
		// --------------
		$objectClass = 'DBObject';
		if (!empty(static::$component['class'])) {
			$objectClass = static::$component['class'];
		}

		// Create object and init that
		// ---------------------------
		$realObject = new $objectClass();

		$realObject->properties = $data;

		// Add to cache pool
		// -----------------
		//static::$databaseObjectsPool[$data['_id']] = $realObject;

		// Return an object
		// ----------------
		return $realObject;
	}



	// Delete object
	// -------------
	public function delete() {

		// Log message
		// -----------
		\Logs::log(array('action' => 'delete', 'class' => @ static::$component['id'], 'object_id' => @$this->_id, 'identity' => $this->getIdentity()), 'objects');


		// Move to trashbin
		// ----------------
		if ($trashModule = @ \Core::getModule('trash')) {
			if ($trashModule->getComponentSettings('useTrash') == true) {
				$trashModule->put(array(
					'class' => static::$component['id'],
					'identity' => $this->getIdentityTitle(),
					'object' => $this->properties,
					'userID' => @ \Core::getModule('users')->user->_id,
					'time' => time()
				));
			}
		}


		// Query
		// -----
		$collection = static::getCollectionName();
		$result = DB::$connection->$collection->remove(array('_id' => $this->_id));
		$this->properties['@deleted'] = true;


		// Event
		// -----
		\Events::send('objectDelete', array('class' => static::$component['id'], 'id' => $this->_id), array('client' => true));

		return $result;
	}

	// Save an object
	// --------------
	public function save($args = array()) {

		// If object marked as deleted, do nothing
		// ---------------------------------
		if (@$this->properties['@deleted'] == true) return;

		// Update create time
		// ------------------
		if (empty($this->properties['@createTime'])) $this->properties['@createTime'] = time();
		$this->properties['@updateTime'] = time();

		// Event
		// -----
		if (@$args['skipEvent'] != true) {
			\Events::send('objectUpdate', array('class' => static::$component['id'], 'id' => $this->_id), array('client' => true));
		}

		// Generate ID
		// -----------
		if (empty($this->properties['_id'])) {
			$this->properties['_id'] = (string) new MongoID();
		}
		else {
			$this->properties['_id'] = (string) $this->properties['_id'];
		}

		// Add owner
		// ---------
		if (empty($this->properties['@owner'])) {
			$userID = @\Core::getModule('users')->user->_id;
			if (!empty($userID)) {
				$this->properties['@owner'] = $userID;
			}
		}


		//var_dump($this);
		//die();

		// Query
		// -----
		$collection = static::getCollectionName();
		$result = DB::$connection->$collection->save($this->properties);

		// Return object ID
		// ----------------
		if ($result != false) return $this->_id;
		else return false;
	}


	// Clone an object
	// ---------------
	public function cloneIt() {
        $objectClass = get_class($this);
		$clone = call_user_func(array($objectClass, $this->properties));
		$clone->_id = (string) new MongoID();
		return $clone;
	}

	// Search objects by query
	// -----------------------
	public static function find($args = array()) {

		// Сборка данных для запроса
		// -------------------------
		$query = first_var(@ $args['query'], array());
		$fields = first_var(@ $args['fields'], array());

		// Require connection
		// ------------------
		$connection = \DB::$connection;
		if (empty($connection)) return;

		$collection = static::getCollectionName();
		$result = $connection->$collection->find($query, $fields);

		// Возврат количества
		// -------------------------------
		if (@ $args['count'] === true) return $result->count();

        if (@ $args['first']) {
            $args['limit'] = 1;
        }

		// Сотировка и ограничение
		// --------------------------------
		if (!empty($args['sort'])) $result->sort($args['sort']);
		if (isset($args['skip'])) $result->skip($args['skip']);

		// Shuffle
		// -------
		if (@ $args['shuffle'] == true) {
			$result = iterator_to_array($result, false);

			if (isset($args['limit'])) $result = array_slice($result, 0, $args['limit'], true);
			shuffle($result);
		}

		else {
			if (isset($args['limit'])) $result->limit($args['limit']);
			$result = iterator_to_array($result, false);
		}

		// Преобразование объектов к их типам
		// -----------------------------------
		if (@ $args['asArray'] != true) {
			foreach($result as $objectKey => $object) {
				$result[$objectKey] = static::getInstance($object);
			}
		}

		// Если нужен один
		// --------------------------------
		if (@ $args['first']) {
			@ reset($result);
			$result = @ current($result);
		}

		// Возврат
		// --------------------------------
		return $result;

	}


	// Search by primary key
	// ----------------------
	public static function findPK($key, $args = array()) {

	  if (empty($key)) return;
	  if (!empty(static::$objectsPool) && is_string($key) && array_key_exists($key, static::$objectsPool)) return static::$objectsPool[$key];

		// Выполняем
		// ---------
		$connection = DB::$connection;
		$collection = static::getCollectionName();
		$result = $connection->$collection->findOne(array('_id' => $key));

		// Превращаем в реальный
		// ---------------------
		if (!empty($result)) {

			if (@ $args['asArray'] == true) {
				return $result;
			}

			return static::getInstance($result);
		}
	}

	// Search one object by query
	// --------------------------
	public static function findOne($args = array()) {
		$args['first'] = true;
		return self::find($args);
	}


	// Bulk object deletion
	// --------------------
	public static function findAndDelete($args = array()) {

		// Запрос должен существовать
		// --------------------------
		if (empty($args['query'])) return false;

		// Удаляем каждый из объектов
		// --------------------------
		$objects = static::find(array('query' => $args['query']));
		if (!empty($objects)) foreach($objects as $object) $object->delete();

		return true;
	}

	// Find, update and save
	// ---------------------
	public static function findAndSave($data = array()) {

		// Если есть идентификатор
		// -----------------------
		if (isset($data['_id'])) {

			// Пробуем считать
			// ---------------
			$object = static::findPK($data['_id']);

			// Если объект прочитан
			// --------------------
			if (!empty($object)) {
				$object->set($data);
				return $object->save();
			}
		}

		// Or create new object
		// --------------------
		$class = get_called_class();
		$object = new $class();
		\Events::send('objectCreated', array('class' => static::$component['id'], 'id' => $object->_id), array('client' => true));
		$object->set($data);
		return $object->save();

	}

	// Find and update objects
	// -----------------------
	public static function findAndUpdate($query = array(), $data = null) {

		// If no any data
		// --------------
		if (!is_array($data)) return false;

		// Read object
		// -----------
		$objects = static::find(array('query' => $query));
		if (empty($objects)) return;

		// Update and save every object
		//  --------------------------
		foreach($objects as $object) {
			$object->set($data);
			$object->save();
		}

		return true;

	}

}
