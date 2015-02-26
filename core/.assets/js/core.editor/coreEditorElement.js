CoreEditorElement = function(args) {

	this.properties = {}; // Properties list are here
	this.elements = {}; // Elements are here
	this.nodes = {}; // Nodes

	Events.call(this);
};

CoreEditorElement.prototype = $.extend({}, Events.prototype, {

	// Update
	// ------
	update : function(args) {

		// This
		// ----
		var editorElement = this;

		// Options
		// -------
		if (args != null) {
			this.properties = args.properties;
			this.editorProperties = args.editorProperties;
			this.elements = args.elements;
			this.editor = args.editor;
			this.nodes = {};
		}

		// Init elements
		// -------------
		if (this.elements != null)
		$.each(this.elements, function(elementIndex, elementData) {

			// Pass editor propeties
			// ---------------------
			elementData.editorProperties = args.editorProperties;
			elementData.editor = args.editor;

			// Create element
			// --------------
			var element = CoreEditorElement.createElement(elementData);
			editorElement.nodes[elementIndex] = element;
		});

		// Bind properties
		// ---------------
		if (this.editorProperties != null) {
			this.bindProperties(this.editorProperties);
		}

	},

	// Update widget
	// -------------
	updateWidget : function() {

		// This
		// ----
		var editorElement = this;

		// Clear
		// -----
		$(this.widget).empty();

		// Update child widgets
		// --------------------
		if (this.nodes != null) {
			$.each(this.nodes, function(nodeIndex, node) {

				// Update element's widget
				// -----------------------
				node.updateWidget();

				// Add widget
				// ----------
				$(editorElement.widget).append(node.widget);
			});
		}
	},

	// Bind property to itself or to subelements
	// -----------------------------------------
	bindProperties : function(editorProperties) {

		var controller = this;

		// Bind single record
		// -------------------
		if (this.property != null) {
			var property = this.editorProperties[this.property];
			if (property != null) property.binding = controller;
		}

		// Or classical form binding
		// -------------------------
		else if (this.properties != null && this.editorProperties != null) {
			// Bind each property
			// ------------------
			$.each(this.properties, function(propertyIndex, propertyID) {
				var property = editorForm.editorProperties[propertyID];
				if (property == null) return;
				property.binding = controller;
			});
		}
	},

	// Set value
	// ---------
	setValue : function(value) {},

	// Get value
	// ---------
	getValue: function() {
		return null;
	}

});

// Create elements
// ---------------
CoreEditorElement.createElement  = function(args) {

	// Switcher
	// --------
	switch(args.type) {
		case 'tabs': return new CoreEditorTabs(args); break;
		case 'tab': return new CoreEditorTab(args);	break;
		case 'controller': return new CoreEditorController(args); break;
		case 'form': return new CoreEditorForm(args); break;
		case 'block': return new CoreEditorBlock(args); break;
		case 'editor': return new CoreEditorFull(args); break;
	}

};
