// Core controller
// ---------------
CoreEditorController = function(args) {

	// Init
	// ----
	CoreEditorElement.call(this, args);

	// Create
	// ------
	this.widget = $('<div/>');
	this.update(args);

};

// Prototype
// ---------
CoreEditorController.prototype = $.extend({}, CoreEditorElement.prototype, {

	// Update
	// ------
	update : function(args) {

		// Options
		// -------
		this.editorProperties = args.editorProperties;
		this.property = args.property;
		this.editor = args.editor;

		// Find controller
		// ---------------
		var controllerClass = window[args.controllerClass];
		if (controllerClass == null) {
			console.warn('Не найден контроллер для связывания');
			return;
		}

		// Create controller
		// -----------------
		this.controller = new controllerClass(args);
		$(this.widget).append(this.controller.widget);

		// Bind
		// ----
		this.bindProperties(args.editorProperties);
	},


	// Bind property to itself or to subelements
	// -----------------------------------------
	bindProperties : function(editorProperties) {

		// This
		// ----
		var editorController = this;

		// Bind single record
		// -------------------
		if (this.property != null) {

			// Form property
			// -------------
			var property = this.editorProperties[this.property];

			// Bind
			// ----
			if (property != null) {
				property.binding = editorController.controller;
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
				property.binding = editorController.controller;

			});

		}

	},

	// Update widget
	// -------------
	updateWidget: function() {
		if (this.controller != null && this.controller.updateWidget != null) {
			this.controller.updateWidget();
		}
	}


});
