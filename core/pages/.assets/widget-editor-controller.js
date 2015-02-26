WidgetEditorController = function(args) {

	// This
	// ----
	var controller = this;

	// Variables
	// ---------
	this.editor = args.editor;
	this.attachSelector();

};


WidgetEditorController.prototype = {

	// Selector
	// --------
	attachSelector : function() {

		var controller = this;

		// Find switcher
		// --------------
		var typeSwitch = this.editor.properties['type'].binding;

		// Listener
		// ---------
		typeSwitch.addListener('change', function(e) {
			if (e.data.id != this.type) {
				controller.changeType(e.data.id);
			}
		});

	},


	// Change type
	// ----------
	changeType : function(type) {

		var controller = this;

		// Get widget type
		// ---------------
		this.type = type;

		// Get new args format
		// -------------------
		API.action({
			'action' : 'module/widgets/getWidgetArgsFormat',
			'data' : this.type,
			'callback' : function(result) {

				// Empty
				// ----
				if (result == null) result = [];

				// Get old values
				// --------------
				var editorValue = controller.editor.getValue();

				// Update resource fields
				// ----------------------
				controller.editor.properties['args']['format'] = result;

				// Update
				// ------
				controller.editor.data = editorValue;
				controller.editor.update();

				// Rebind selector
				// ---------------
				controller.attachSelector();
			}

		})

	}

};
