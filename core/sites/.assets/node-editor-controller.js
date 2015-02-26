NodeEditorController = function(args) {

	// This
	// ----
	var controller = this;

	// Variables
	// ---------
	this.editor = args.editor;
	this.attachSelector();

};


NodeEditorController.prototype = $.extend({}, {

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
			'action' : 'module/sites/getNodeDataFormat',
			'data' : this.type,
			'callback' : function(result) {

				// If empty, return
				// ----------------
				if (result == null) return;

				// Get old values
				// --------------
				var editorValue = controller.editor.getValue();

				controller.editor.properties.data = {'type' : 'record', 'format' : result.properties};

				// Set old data, update editor and rebind selector
				// -----------------------------------------------
				controller.editor.data = editorValue;
				controller.editor.update();
				controller.attachSelector();
			}

		})

	}

});
