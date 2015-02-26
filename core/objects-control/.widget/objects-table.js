ObjectsTable = function(args) {

	// Link to this
	// ------------
	var objectsTable = this;

	// Nothing to do
	// -------------
	if (args == null || args.widget == null) return;

	// Get widget
	// ----------
	$.extend(this, args);
	this.widget = $(args.widget);

	// Set options
	// -----------
	this.state = args.state;
	if (this.state == null) this.state = {};
	

	// Link to sortable fields
	// -----------------------
	$(this.widget).find(".objects-table .core-table-heading .field-heading").each(function() {

		// Field ID
		// --------
		var fieldID = $(this).attr('data-field-id');

		// Sort
		// ----
		$(this).click(function() {
			objectsTable.sort(fieldID);
		});
	});

	// Link to checkbox
	// ----------------
	$(this.widget).find(".object-checkbox").each(function() {
		var checkbox = this;
		$(this).change(function() {
			var objectID = $(checkbox).attr('data-object-id');	
			objectsTable.toggleSelection(objectID, $(checkbox).is(':checked'));
		});
	});

	// Link to action buttons
	// ----------------------
	$(this.widget).find(".objects-table-actions .action-button").each(function() {

		var actionButton = this;

		// Action ID
		// ---------
		var action = $(this).attr('data-action');

		// Execute
		// -------
		$(this).click(function() {
			if ($(actionButton).attr('data-link') != null) {
				window.location = $(actionButton).attr('data-link');
			}
			else {
				objectsTable.action(action);
			}
		});
	});

}

// Prototype
// ---------
ObjectsTable.prototype = {

	// Sort
	// ----
	sort : function(fieldID) {

		// Sort options
		// ------------
		var sort = {};
		if (this.state.sort == null) this.state.sort = {};

		// If we already have sort with this field, switch it
		// --------------------------------------------------
		if (this.state.sort[fieldID] != null) {
			var val = this.state.sort[fieldID];
			if (val == 1) val = -1; else val = 1;
			sort = {};
			sort[fieldID] = val;
		}

		// Or add new
		// ----------
		else {
			sort = {};
			sort[fieldID] = 1;
		}

		// Update
		// ------
		this.state.sort = sort;
	
		// Append the state
		// ----------------
		API.request({
			'uri' : '/module/session/set',
			'data' : {'id' : this.id,	'data' : {'sort' : this.state.sort},	'mode' : 'update', 'reload' : true},
		});

	},


	// Add selection
	// -------------
	toggleSelection : function(objectID, value) {

		// Grarantee selection
		// -------------------
		if (this.state.selection == null) this.state.selection = {};

		// Toggle
		// ------
		if (value) {
			this.state.selection[objectID] = true;
			$(this.widget).find(".core-table-row[data-object-id=" + objectID + "]").addClass("active");
		}                      

		else {
			delete this.state.selection[objectID];
			$(this.widget).find(".core-table-row[data-object-id=" + objectID + "]").removeClass("active");
		}

		// Append the state
		// ----------------
		API.request({
			'uri' : '/module/session/set',
			'data' : {'id' : this.id,	'data' : {'selection' : this.state.selection },	'mode' : 'update'},
		});


	},

	// Execute action
	// --------------
	action : function(actionID) {

		if (this.state.selection == null) this.state.selection = {};

		var list = [];

		// Add
		// ---
		$.each(this.state.selection, function(id, value) {
			list.push(id);
		});

		// Send request
		// ------------
		API.request({
			'uri' : '/module/objects-control/applyAction',
			'reload' : true,
			'data' : {
				'action' : actionID,
				'class' : this.state.class,
				'objects' : list,
			}
		});
	}

}                               
