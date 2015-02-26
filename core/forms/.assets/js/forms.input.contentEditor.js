UI.FormInputs.contentEditor = function(args) {
	UI.FormInput.call(this, args);

	// Create editor
	// --------------
	var editorComponent = Components.get('input', 'contentEditor');
	this.editor = editorComponent.getInstance({
		'value' : this.value
	});
	// Assign widget
	// -------------
	this.widget = this.editor.widget;
	this.initWidget();
};

UI.FormInputs.contentEditor.prototype = $.extend({}, UI.FormInput.prototype, {

	// Set value
	// ---------
	setValue : function(value) {
		this.editor.setValue(value);
	},

	// Get value
	// ---------
	getValue : function() {
		return this.editor.getValue();
	}
});

