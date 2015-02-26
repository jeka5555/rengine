// Form
// ----
CoreEditorForm = function(args) {
	// Init
	// ----
	CoreEditorElement.call(this, args);
	this.property = null; // Single bindable property

	// Create
	// ------
	this.widget = $('<div></div>');
	this.update(args);
};

CoreEditorForm.prototype = $.extend({}, CoreEditorElement.prototype, {

	// Update
	// ------
	update : function(args) {

		// Options
		// -------
		this.properties = args.properties;
		this.property = args.property;
		this.editorProperties = args.editorProperties;
		this.editor = args.editor;

		// Create form
		// -----------
		this.bindProperties(args.editorProperties);
	},

	// Update UI
	// ---------
	updateWidget: function() {

		var editorForm = this;

		// Empty widget
		// ------------
		$(this.widget).empty();

		// Create new
		// ----------
		this.form = new UI.Form({'addID' : true, 'showHints' : true	});

		// Single property
		// ---------------
		if (this.property != null) {

			var rootPropertyID = this.property;

			// Guard
			// -----
			if (
				editorForm.editorProperties[this.property] == null ||
				editorForm.editorProperties[this.property].binding  != this
			) return;

			// Convert each format value to input
			// -----------------------------------
			if (editorForm.editorProperties != null && editorForm.editorProperties[this.property] != null && editorForm.editorProperties[this.property].format != null)
			$.each(editorForm.editorProperties[this.property].format, function(propertyID, property) {

				var inputFormat = property;

				// Append value
				// ------------
				if (editorForm.editorProperties[rootPropertyID].value != null && editorForm.editorProperties[rootPropertyID].value[propertyID] != null )
					inputFormat.value = editorForm.editorProperties[rootPropertyID].value[propertyID];

				editorForm.form.appendInput(propertyID, inputFormat);
			});

			// Bind to whole form
			// ------------------
			editorForm.editorProperties[this.property].binding = editorForm.form;
		}

		// For list of properties
		// ----------------------
		else if (this.properties != null) {

			// Create inputs
			// --------------
			if (editorForm.editorProperties != null)
			$.each(editorForm.editorProperties, function(propertyID, property) {

				if (property.binding != editorForm) return;

				// Add input
				// ---------
				editorForm.form.appendInput(propertyID, property);
				property.binding = editorForm.form.inputs[propertyID];
			});

		}

		// Add form
		// --------
		$(this.widget).append(this.form.form);

	},

	// Get value
	// ---------
	getValue : function() {

		var value = this.form.getValue();

		var result = {};

		// Clean up values
		// ---------------
		$.each(value, function(valueIndex, valueData) {
			if (valueData != null) result[valueIndex] = valueData;
		});

		// Return result
		// -------------
		return result;
	},


	// Set value
	// ----------
	setValue : function(args) {

		// This
		// ----
		var formElement = this;

		// Set values
		// ----------
		$.each(this.form.inputs, function(inputID, input) {
			if (args == null || args[inputID] == null) input.setValue(null);
			else input.setValue(args[inputID]);
		});
	},


	// Bind property to itself or to subelements
	// -----------------------------------------
	bindProperties : function(editorProperties) {

		// This
		// ----
		var editorForm = this;

		// Bind single record
		// -------------------
		if (this.property != null) {

			// Form property
			// -------------
			var property = this.editorProperties[this.property];

			// Bind
			// ----
			if (property != null) {
				property.binding = editorForm;
			}
		}

		// Or classical form binding
		// -------------------------
		else if (this.properties != null && this.editorProperties != null) {


			// Bind each property
			// ------------------
			$.each(this.properties, function(propertyIndex, propertyID) {

				// Get property
				// ------------
				var property = editorForm.editorProperties[propertyID];
				if (property == null) return;

				// Bind
				// ----
				property.binding = editorForm;

			});

		}

	}


});
