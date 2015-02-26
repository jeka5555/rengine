<?php

namespace Core\Widgets;

class ObjectsTable extends \Widget {

	// Component description
	// ---------------------
	public static $component = array('id' => 'objects-table');

	// Add controller script
	// ---------------------
	public function addControllerScript() {


		// Parent controller
		// -----------------
		parent::addControllerScript();

		// Generate id
		// -----------
		$objectTableID = 'objectTable'.uniqid();

		// Build table conroller data
		// --------------------------
		$scriptData = array(
			'widget' => '#'.$this->generateHtmlID(),
			'id' => @ $this->args['id'],
			'state' => @ $this->args,
			'class' => @ $this->args['class'],
			'actionURI' => @ $this->args['actionURI']
		);

		// Submit controller script
		// ------------------------
        \Events::send('addEndScript', ' var '.$objectTableID.' = new ObjectsTable('.json_encode($scriptData).'); ');
	}

	// Get fields list with complete options format
	// --------------------------------------------
	public function collectFields() {

		// Collect here
		// ------------
		$fields = array();

		// Get class actions
		// -----------------
		$class = $this->class;
        $classFields = $class::getInstance()->getClassProperties();


		// Collect field's list from passed args
		// -------------------------------------
		if (!empty($this->args['fields'])) {
			foreach($this->args['fields'] as $fieldID => $field) {

				// Get field ID
				// ------------
				if (is_string($field)) $fieldID = $field;
				else if (is_array($field) && !empty($field['id'])) $fieldID = $field['id'];	
				else $fieldID = $fieldID;

				// Add with calculated ID
				// ----------------------
				$fields[$fieldID] = $field;
			}
		}

		// Get from class definition
		// --------------------------
		else if (!empty($classFields)) {
			foreach($classFields as $classField) {
				if (@ $classField['listing'] == true) {
					$fields[$classField['id']] = $classField;
				}
			}
		}

		// Complete field definitions from class
		// -------------------------------------
		if (!empty($classFields)) {
			foreach($classFields as $fieldID => $field) {

				if (!empty($fields[$fieldID])) {	

					// Merge fields data
					// -----------------
					if (is_string($fields[$fieldID])) $fields[$fieldID] = $field;
						else if (is_array($fields[$fieldID])) $fields[$fieldID] = array_merge($field, $fields[$fieldID]);
				}
			}
		}

		// Return fields
		// -------------
		return $fields;
	
	}

	// Render a table heading
	// ----------------------
	public function renderHeading() {

		// We require some real fields list
		// --------------------------------
	  if (!is_array($this->fields) || empty($this->fields)) return;

		// Generate field's content
		// ------------------------
		$content = '';

		// Select button
		// -------------
		if (@ $this->args['addSelect'] == true) $content .= '<div>Выбор</div>';
		
		// Fields from list
		// ----------------
		foreach($this->fields as $fieldID => $field) {
			$title = first_var(@$field['title'], $fieldID);

			// Add sorting options
			// -------------------
			$sortable = '';
			if ( $this->args['sortable'] !== false and @ $field['sortable'] == true) {
				$title = '<a href="javascript:void(0)">'.$title.'</a>';
				$sortable = ' sortable';
				if (!empty($this->args['sort'][$fieldID])) {
					if ($this->args['sort'][$fieldID] == -1) $sortable .= ' sorted-down';
					else $sortable .= ' sorted-up';
				}
			}

			$content .= '<div class="field-heading '.$sortable.'" data-field-id="'.$fieldID.'">'.$title.'</div>';
		}

		// Controls
		// --------
		if (@ $this->args['addControls'] == true) $content .= '<div></div>';
		
		// Output content
		// --------------
		$content = '<div class="core-table-heading">'.$content.'</div>';
		return $content;

	}

	// Render a table content
	// ----------------------
	public function renderTable() {

		$class = $this->class;

        $limit = null; $skip = 0;

		// Prepare the query
		// -----------------
		$query = array();
		if (!empty($this->args['query'])) $query = $this->args['query'];
	
		// Prepare a sort options
		// ----------------------
		$sort = array();
		if (is_array(@$this->args['sort'])) $sort = $this->args['sort'];

        if (!empty($this->args['pageLimit'])) $limit = $this->args['pageLimit'];

        if (!empty($this->args['page'])) $skip = $limit * ($this->args['page'] - 1 );

		// Load objects
		// ------------
		$objects = $class::find(array('query' => $query, 'sort' => $sort, 'skip' => $skip, 'limit' => @$limit));
		if (empty($objects)) return;

		// Output objects
		// --------------
		$content = '';
		foreach($objects as $object) {

			$objectContent = '';

			// View URI
			// --------
			$viewURI = null;
			if (!empty($this->args['viewURI'])) $viewURI = call_user_func($this->args['viewURI'], $object);
	
			                
			// Collect class data here
			// -----------------------
			$htmlClass = '';

			// Select button
			// -------------
			if (@ $this->args['addSelect'] == true) {

				// Switch check state
				// ------------------
				$checkAddin = '';
				$state = @ $this->args['selection'][$object->_id];
				if (!empty($state)) {
					$checkAddin = ' checked="checked"';
					$htmlClass .= " active";
				}

				$objectContent .= '<div><input type="checkbox" class="object-checkbox" data-object-class="'.$this->args['class'].'" data-object-id="'.$object->_id.'" '.$checkAddin.'/></div>';
			}
			

			// Return all fields from list
			// ---------------------------
			foreach($this->fields as $fieldID => $field) {

				// Get value
				// ---------
				if (!empty($field['valueFunction']) && is_callable($field['valueFunction'])) $value = call_user_func($field['valueFunction'], $object);
				else $value = @ $object->get($fieldID);

				// From function
				// -------------
				if (isset($field['formatFunction']) && is_callable($field['formatFunction'])) $value = call_user_func($field['formatFunction'], $value, $object);

				// Else, detect type
				// -----------------
				elseif (empty($field['valueFunction'])) {
					// Get value
					// ---------
					$value = \DataView::get('field', $value, $field);
				}

				// View URI
				// --------
				if (!empty($this->args['viewFields'])) {
					$viewFields = $this->args['viewFields'];
					if (is_string($viewFields) && $fieldID == $viewFields) $value = '<a href="'.$viewURI.'">'.$value.'</a>';
					else if (is_array($viewFields) && in_array($fieldID, $viewFields)) $value = '<a href="'.$viewURI.'">'.$value.'</a>';
				}

				// Return column
				// -------------
				$objectContent .= '<div class="field-'.$fieldID.'">'.$value.'</div>';
			}

			// Select button
			// -------------
			if (@ $this->args['addControls'] == true) $objectContent .= '<div>'.$this->renderObjectControls($object).'</div>';


			// Class function
			// --------------
			if (!empty($this->args['htmlClassFunction'])) $htmlClass = call_user_func($this->args['htmlClassFunction'], $object);

			// Finish formating
			// ----------------
			$content .= '<div class="core-table-row '.$htmlClass.'" data-object-id="'.$object->_id.'">'.$objectContent.'</div>';
		}

		// Return
		// ------
		return $content;

	}

	// Render full object controls
	// ---------------------------
	public function renderObjectControls($object) {

		$content = '';
		$editLink = '/modules/db/edit/'.$this->args['class'].'/'.$object->_id;
		$deleteLink = '/modules/db/delete/'.$this->args['class'].'/'.$object->id;

		// Edit button
		// -----------
		if (!empty($this->args['editorURI'])) {			
			if (is_callable($this->args['editorURI'])) $editLink = $this->args['class'].'/'.$object->_id;
		}

		// Buttons
		// -------
		$content .= '<a class="button button-icon edit-object" href="'.$editLink.'"></a>';
		$content .= '<a class="button button-icon delete-object" href="'.$deleteLink.'"></a>';

		// Control panels
		// --------------
		return '<div class="object-controls" data-class="'.$this->args['class'].'" data-id="'.$object->_id.'">'.$content.'</div>';
	}

	// Render an actions buttons
	// -------------------------
	public function renderActions() {
	
		if (@ $this->args['addActions'] != true) return;

		// Get class actions
		// -----------------
        $class = $this->class;
        $classComponent = $class::getInstance()->getClassProperties();
		$classActions = @ $classComponent['actions'];
		if (empty($classActions)) $classActions = array();

		$class = $this->class;

		// Here we collect content
		// -----------------------
		$content = '';

		$actions = array();

		// If we have an list of actions,
		// ------------------------------
		if (!empty($this->args['actions'])) {
			foreach($this->args['actions'] as $actionID => $action) {
				if (is_string($action)) $actions[$action] = array();
				else if (is_array($action)) $actions[$actionID] = $action;
			}
		}

		// If we have class
		// ----------------
		else {
			foreach($classActions as $actionID => $action) {
				$actions[$actionID] = $action;
			}
		}


		foreach($classActions as $actionID => $action) {

			if (!empty($actions[$actionID])) $actions[$actionID] = array_merge($action, $actions[$actionID]);
			else $actions[$actionID] = $action;

			// Filter wrong actions
			// --------------------
			$access = $class::checkClassAccess($actionID);
			if (@$action['bulk'] != true || !$access) {
				unset($actions[$actionID]);
				continue;
			}

		}

		// Add buttons
		// -----------
		if (!empty($actions))
		foreach ($actions as $actionID => $action) {

			// Detect title
			// ------------
			$title = first_var(@ $action['title'], $actionID);

			// External link
			// -------------
			$linkAddin = '';
			if (!empty($action['link'])) {
				$linkAddin = ' data-link="'.$action['link'].'" ';
			}

			// Action button
			// -------------
			$content .= '<input type="submit" '.$linkAddin.' class="button action-button" data-action="'.$actionID.'" value="'.$title.'" />';
		}

		// Return buttons
		// --------------
		return '<div class="objects-table-actions">'.$content.'</div>';
	
	}


	// Apply additional settings
	public function applySettings() {

		// Requirements
		// ------------
		if (empty($this->args['id'])) return;
		$tableArgs = @ $_GET['objects-table-'.$this->args['id']];
		if (empty($tableArgs)) return;

		// Apply
		// -----
		if ($this->args = array_merge($this->args, $tableArgs));

	}


	// Init session data
	// -----------------
	public function initSession() {

		// Need to have session var
		// ------------------------
		$id = @ $this->args['id'];
		if (empty($id)) return;

		if (!empty($_SESSION[$id])) {
			$this->args = array_merge($this->args, $_SESSION[$id]);
		}
		

	}

    // Render paginator
    // ----------------
    private function renderPaginator() {

        if (@$this->usePaginator != true) return;

        $content = '';

        // Страничная навигация
        // --------------------
        if(isset($this->args['objects'])) {
            $childrenCount = count($this->args['objects']);
        } else {
            $class = $this->class;
            $childrenCount = $class::find(array('query' => $this->args['query'], 'sort' => $this->args['sort'], 'count' => true));
        }

        if ($childrenCount > $this->pageLimit) {

            // Resource list widget is here
            // ---------------------------
            $nodeListWidget = $this;

            $paginator = \Core::getModule('widgets')->createWidget(array(
                'paginator',
                array(
                    'count' => ceil($childrenCount / $this->pageLimit),
                    'active' => $this->page,
                    'function' => @ $this->args['pageFunction']
                )
            ));

            // Add page navigation
            // -------------------
            $content .= $paginator->get();
        }

        return $content;

    }


	// Render this widget
	// ------------------
	public function render() {

		// Apply session data
		// ------------------
		$this->initSession();

		// Try to load required class
		// --------------------------
		if (empty($this->args['class'])) return;
		$this->class = \Core::getComponent('class', $this->args['class']);
		if (empty($this->class)) return;

        // Pagination
        // ----------
        $this->usePaginator = first_var(@ $this->args['usePaginator'], true);
        $this->pageLimit = first_var(@ $this->args['pageLimit'], 10);
        $this->page = first_var(@ $this->args['page'], 1);
        $this->parent = @ $this->args['parent'];
		
		$this->applySettings();

		// Generate unique id
		// -----------------
		$id = first_var(@ $this->args['id'], uniqid());


		// Collect field's list
		// --------------------
		$this->fields = $this->collectFields();

		// Get heading content
		// -------------------
		$headingContent = $this->renderHeading();

		// Get table content
		// -----------------
		$tableContent = $this->renderTable();

		// Get actions content
		// -------------------
		$actionsContent = $this->renderActions();

		// Generate table class
		// --------------------
		$tableClass="objects-table-".$this->args['class'];

        // Render paginator
        // ----------------
        $paginator = $this->renderPaginator();

		// Return the table
		// ----------------
		return '
		<div id="objects-table-'.$id.'" class="objects-table-wrap">
			<div  class="core-table objects-table '.$tableClass.'">
				'.$headingContent.'
				'.$tableContent.'
			</div>
			'.$paginator.'
			'.$actionsContent.'
		</div>';
	}
}