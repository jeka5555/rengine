// Core object editor
// ------------------
CoreEditor = function(args) {

	this.elements = {};

	CoreEditorElement.call(this);

	// Create widget
	// -------------
	this.widget = $('<div class="core-editor"></div>');

	// Update
	// ------
	this.update(args);

};

// Core editor prototype
// ---------------------
CoreEditor.prototype = $.extend({}, CoreEditorElement.prototype, {

	// Get editor value
	// ----------------
	getValue : function() {

		// Get data
		// --------
		var value = {};

		if (this.properties != null)
		$.each(this.properties, function(propertyID, property) {

			// Skip non-binded properties
			// --------------------------
			if (property.binding == null) return;

			// Read value
			// ----------
			var propertyValue = property.binding.getValue();
			value[propertyID] = propertyValue;
		});

		return value;

	},


	// Get value of single property
	// ----------------------------
	getPropertyValue : function(propertyID) {
		if (this.properties == null || this.properties[propertyID] == null) return null;
		var prop = this.properties[propertyID];

		// Return value
		// ------------
		if (prop.binding != null) return prop.binding.getValue();
		else if (prop.value != null) return prop.value;
	},

	// Update
	// ------
	update : function(args) {

		// This
		// ----
		var editor = this;

		// Options
		// -------
		if (args != null) {
			this.data = args.data;
			this.properties = args.properties;
			this.nodes = {};
			this.elements = args.elements;
			this.jsController = args.jsController;
		}

		// Apply data to properties
		// ------------------------
		if (this.data != null) {
			$.each(this.data, function(propertyID, propertyValue) {
				if (editor.properties != null && editor.properties[propertyID] != null) {
					editor.properties[propertyID].value = propertyValue;
				}
			});
		}

		// Set default structure
		// ---------------------
		if (this.elements == null) {

			// Element is form
			// ---------------
			var element = {'type' : 'form', 'properties' : []};

			// Add each property to this form
			// ------------------------------
			if (this.properties != null)
			$.each(this.properties, function(propertyIndex, propertyID) {
				element.properties.push(propertyIndex);
			});

			this.elements = [element];
		}

		// Init elements
		// -------------
		$.each(this.elements, function(elementIndex, elementData) {

			// Pass editor propeties
			// ---------------------
			elementData.editorProperties = editor.properties;
			elementData.editor = editor;

			// Create element
			// --------------
			var element = CoreEditorElement.createElement(elementData);
			editor.nodes[elementIndex] = element;
		});

		// Rebuild UI
		// ----------
		this.updateWidget();

		// Attach controller
		// -----------------
		if (this.jsController != null) {

			// Get controller class
			// --------------------
			var contollerClass = window[this.jsController];
			if (contollerClass == null) return;

			// Attach controller
			// -----------------
			var controller = new contollerClass({'editor' : this});
		}

		// Ready event
		// -----------
		this.callEvent('ready');

	},

	// Refresh editor
	// --------------
	setValue : function(value) {

		var editor = this;

		if (value == null) return;

		$.each(value, function(propertyID, propertyValue) {

			// Get property
			// ------------
			var property = editor.properties[propertyID];
			if (property == null) return;

			// Set value
			// ---------
			property.value = propertyValue;

			// Get binding
			// -----------
			if (property.binding == null) return;
			property.binding.setValue(propertyValue);
		});
	},

	// Set property value
	// ------------------
	setPropertyValue: function(propertyID, value) {

		// Get property
		// ------------
		var property = this.properties[propertyID];
		if (property == null || property.binding == null) return;

		// Update
		// ------
		property.binding.setValue(value);
	}

});
