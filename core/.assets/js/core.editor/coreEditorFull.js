// Single tab
// ----------
CoreEditorFull = function(args) {

	CoreEditorElement.call(this);

	this.widget = $('<div>full editor</div>');
	this.update(args);


};

CoreEditorFull.prototype = $.extend({}, CoreEditorElement.prototype, {



	// Update
	// ------
	update : function(args) {

		// Options
		// -------
		this.properties = args.properties;
		this.property = args.property;
		this.editorProperties = args.editorProperties;

		// Args
		// ----
		var editorArgs = {'structure' : args.structure};

		if (this.property != null) {
			editorArgs['properties'] = this.editorProperties[this.property].format;
		}

		this.editor = new CoreEditor(editorArgs);

		// Create form
		// -----------
		this.bindProperties(args.editorProperties);

	},


	updateWidget : function() {

		var editorFull = this;

		// Empty widget
		// ------------
		$(this.widget).empty();
		$(this.widget).append(this.editor.widget);

	},

	// Get value
	// ---------
	getValue : function() {

		var value = this.editor.getValue();
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
		this.editor.setValue(args);
	},


	// Bind property to itself or to subelements
	// -----------------------------------------
	bindProperties : function(editorProperties) {

		// This
		// ----
		var editorFull = this;

		// Bind single record
		// -------------------
		if (this.property != null) {

			// Form property
			// -------------
			var property = this.editorProperties[this.property];

			// Bind
			// ----
			if (property != null) {
				property.binding = editorFull;
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
				var property = editorFull.editorProperties[propertyID];
				if (property == null) return;

				// Bind
				// ----
				property.binding = editorFull.editor.properties.binding;

			});

		}

	}
});
