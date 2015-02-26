UI.FormInputs.list = function(args) {

	// Init
	// ----
	var input = this;
	UI.FormInput.call(this, args);

	// Elements
	// --------
	this.inputs = [];

	// Widgets
	// -------
	this.widget = $('<div/>');
	this.elementsWidget = $('<div class="list-elements"/>').appendTo(this.widget);
	this.previewWidget = $('<div class="list-preview"/>').appendTo(this.widget);
	this.toolbarWidget = $('<div class="toolbar"/>').appendTo(this.widget);
	this.listElementClass = safeAssign(this.format.listElementClass, 'ListElementClass');

	// Init widget
	// ----------
	this.initWidget();

	// Set options
	// -----------
	this.mode = safeAssign(args.format.mode, 'preview');

	// Set value
	// ---------
	if (this.value != undefined && this.value.length > 0) {
		for (var itemIndex in this.value) {
			input.addElement({ 'value' : this.value[itemIndex], 'format' : input.format.format});
		}
	}

	// Set mode
	// --------
	this.updateToolbar();
	this.setMode(this.mode);

}


// List prototype
// --------------
UI.FormInputs.list.prototype = $.extend({}, UI.FormInput.prototype, {

// Init toolbar
	// ------------
	updateToolbar: function() {

		// Input
		// -----
		var input = this;

		// Toggle mode
		// -----------
		var modeButton = new FlexButton({'title' : 'Сменить режим вида', 'class' : 'toggle-mode', 'mode' : 'icon', 'toggle' : true, 'click' : function() {
			// Set mode
			// --------
			if (input.mode == 'preview') input.setMode('full');
			else input.setMode('preview');
		}});

		// Add button
		// ----------
		var addButton = new FlexButton({'title' : 'Добавить элемент', 'mode' : 'icon', 'class' : 'add-element', 'click' : function() {
			// switch mode
			// -----------
			if (input.mode != 'full') {
				input.setMode('full');
			}
			input.addElement({'format' : input.format.format});
		}});


		// Append toolbar
		// --------------
		$(this.toolbarWidget).empty();
		$(this.toolbarWidget).append(modeButton.widget);
		$(this.toolbarWidget).append(addButton.widget);
	},

	// Set display mode
	// ----------------
	setMode: function(mode) {

		// Set state
		// ---------
		this.mode = mode;

		// Select between mode
		// -------------------
		switch (mode) {

			// Preview mode
			// ------------
			case "preview":
				$(this.elementsWidget).hide();

				var valueWidget = this.getPreviewWidget();
				$(this.previewWidget).empty().append(valueWidget).show();
				break;

			// Full mode
			// ---------
			case "full":
				$(this.elementsWidget).show();
				$(this.previewWidget).hide();
				break;

		}
	},

	// Get preview value
	// -----------------
	getPreviewWidget: function() {

		// This
		// ----
		var input = this;

		// Collect values here
		// -------------------
		var preview = $('<span class="list-preview"/>');

		// Get input's preview value
		// -------------------------
		$.each(this.inputs, function(id, input) {


			var elementPreview = $('<span class="list-preview-element"/>');

			// Get input value
			// ---------------
			var value = input.input.getPreviewValue();

			// Skip empty values
			// -----------------
			if (value == null) return;

			// Append
			// ------
			$(elementPreview).append($('<span class="value"></span>').html(value)).append("&#44;&nbsp;&nbsp;");
			$(preview).append(elementPreview);
		});

		// Return preview widget
		// ---------------------
		return preview;
	},


	// Create element
	// --------------
	addElement : function(args) {

		var input = this;

		// Get element class
		// -----------------
		var elementClass = window[input.listElementClass];
		if (elementClass == null) return;

		// Create element and pass value to it
		// -----------------------------------
		var element = new elementClass({'format' : args.format, 'value' : args.value});

		// Create delete button
		// --------------------
		var deleteButton = new FlexButton({'title' : 'Удалить элемент', 'mode' : 'icon', 'class' : 'delete-element', 'click' : function() {
			// Remove
			// ------
			var position = $.inArray(element, input.inputs);
			if (position != -1) {

				// Remove from array
				// -----------------
				input.inputs.splice(position, 1);



				// Remove widget
				// -------------
				$(element.widget).remove();

			}
		}});

		// Append
		// ------
		$(element.widget).children('.list-element-controls').append(deleteButton.widget);


		// Add to elements
		// ---------------
		this.inputs.push(element);

		// Append to container
		// -------------------
		$(input.elementsWidget).append(element.widget);

	},


	// Get input value
	// ---------------
	getValue : function() {

		var data = [];


		// Get each input value
		// --------------------
		if (this.inputs != null && this.inputs.length > 0) {

			for (var inputIndex in this.inputs) {
				if (this.inputs[inputIndex] != null)
					var value  = this.inputs[inputIndex].getValue();
					if (value != undefined) data.push(value);
			}
		}

		// Skip empty
		// ----------
		if (data.length < 1) return null;

		// Return
		// ------
		return data;
	}


});


ListElementClass = function(args) {

	var element = this;

	// Create widget
	// -------------
	this.widget = $('<div class="list-element core-framed-element">' +
		'<div class="list-element-body"></div>' +
		'<div class="list-element-controls"></div>' +
		'</div>');

	// Subwidgets
	// ----------
	this.controlsWidget = $(this.widget).find(".list-element-controls");
	this.elementBody = $(this.widget).find(".list-element-body");

	// Create content
	// --------------
	this.input = UI.Form.prototype.getInput(args);
	$(this.input.widget).appendTo(this.elementBody);


}

ListElementClass.prototype = $.extend({}, Events.prototype, {

	// Return value
	// ------------
	getValue : function() {

		// For empty inputs, return empty value
		// ------------------------------------
		if (this.input == null) return null;

		// Or just get what we have
		// ------------------------
		return this.input.getValue();
	}

})
