<?php


class SearchableClass extends EditableClass {

	// Index list
	// ----------
	public static $indexes = null;


	// Init component
	// --------------
	public static function initComponent() {

		// Parent's initialization
		// -----------------------
		parent::initComponent();

		// Prepare indexes
		// ---------------
		if (!empty(static::$indexes)) {
			$connection = \DB::$connection;
			$collection = static::getCollectionName();
			foreach(static::$indexes as $index) {
				if (!isset($index[1])) $connection->$collection->ensureIndex(@$index[0]);
				else $connection->$collection->ensureIndex(@$index[0], @$index[1]);
			}

		}
	}


	// Get search query if some filters are given
	// ------------------------------------------
	public static function getSearchQuery($filters) {

		// Final query
		// -----------
		$query = array();

		// Get properties
		// --------------
		$properties = self::getInstance()->getClassProperties();
		if (empty($properties)) return null;


		// Remove empty values
		// -------------------
		foreach($filters as $filterID => $filterValue) {

			// Unset empty
			// -----------
			if ($filterValue == null) {
				unset($filters[$filterID]);
				continue;
			}

			// Skip text
			// ----------
			if ($filterID == '@text') continue;

			// Get property format
			// -------------------
			$propertyFormat = @$properties[$filterID];
			if (  empty($propertyFormat)) {
				unset($filters[$filterID]);
				continue;
			}


			// Process
			// -------
			switch ($propertyFormat['type']) {

				// Number
				// ------
				case 'number':
					$query[$filterID] = $filterValue;
					break;

				// Flag
				//  ---
				case 'boolean':
					$query[$filterID] = $filterValue;
					break;

				// Text
				// ----
				case 'text':
					$query[$filterID] = new MongoRegex('/'.$filterValue.'/iu');
					break;

                default:
                    $query[$filterID] = $filterValue;
                    break;
			}

		}

		// Add text OR
		// -----------
		if (!empty($filters['@text'])) {

			$or = array();

			foreach($properties as $propertyID => $property) {
				if ($property['type'] == 'text') {
					$or[] = array($propertyID => new MongoRegex('/'.$filters['@text'].'/iu'));
				}
			}

			if (!empty($or)) $query['$or'] = $or;
		}

		return $query;

	}

}