UI.FormInputs.record = function(args) {

	// Init record
	// -----------
	UI.FormInput.call(this, args);
	var input = this;

	// Any value must exists
	// ---------------------
	if (this.value == null) this.value = {};

	// Inputs
	// ------
	this.inputs = {};

	// Create widget
	// -------------
	this.widget = $('<div class="form-input-record"></div>');

	// Set options
	// -----------
	this.extendable = safeAssign(args.format.extendable, false);
	this.showID = safeAssign(args.format.showID, true);
	this.mode = safeAssign(args.format.mode, 'preview');

	// Additional widgets
	// ------------------
	this.toolbarWidget = $('<div class="toolbar"/>').appendTo(this.widget);
	this.elementsWidget = $('<div class="record-elements"/>').appendTo(this.widget);
	this.previewWidget = $('<div class="preview-widget"/>').appendTo(this.widget);

	// Value
	// -----
	this.setValue(args.value);

	// Init toolbar
	// ------------
	this.updateToolbar();

	// Switch mode
	// -----------
	this.setMode(this.mode);

}

UI.FormInputs.record.prototype = $.extend({}, UI.FormInput.prototype, {


	// Init toolbar
	// ------------
	updateToolbar: function() {

		// Input
		// -----
		var input = this;

		// Create toolbar
		// --------------
		var toolbar = new Flex.Toolbar({'htmlClass' : 'record-toolbar'});

		// Toggle mode
		// -----------
		toolbar.appendButton({
			'mode' : 'icon',
			'toggle' : true,
			'id' : 'toggle-mode',
			'title' : 'Изменить режим',
			'click' : function() {
				if (input.mode == 'preview') input.setMode('full');
				else input.setMode('preview');
			}
		});

		// Open in external windows
		// ------------------------
		toolbar.appendButton({
			'mode' : 'icon',
			'id' : 'edit-in-window',
			'title' : 'Редактировать в окне',
			'click' : function() {
				input.openEditor();
			}
		});

		// Append toolbar
		// --------------
		$(this.toolbarWidget).empty().append(toolbar.widget);
	},



	// Open editor
	// -----------
	openEditor : function() {

        // Input
        // -----
        var input = this;

        // Create editor window
        // --------------------
		var editor = new RecordElementEditor({
            'format' : input.format.format,
            'value' : input.getValue()
        });

        // Listen for closing
        // -----------------
        editor.addListener('complete', function(e) {
            input.setValue(e.data);
        });
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

		// Collect values here
		// -------------------
		var preview = $('<span class="record-preview"/>');

		// Get input's preview value
		// -------------------------
		$.each(this.inputs, function(id, input) {

			var inputPreview = $('<span class="record-input-preview"/>');

			// Get title
			// ---------
			var title = id;
			if (input.format.title != null) title = input.format.title;

			// Get input value
			// ---------------
			var value = input.getPreviewValue();

			// Skip empty values
			// -----------------
			if (value == null) return;

			// Append
			// ------
			$(inputPreview).append('<span class="title">' + title + ':</span>').append($('<span class="value"></span>').html(value)).append("&#44;&nbsp;&nbsp;");
			$(preview).append(inputPreview);
		});

		// Return preview widget
		// ---------------------
		return preview;
	},


	// Set value
	// ---------
	setValue: function(object) {

		var input = this;
		this.inputs = {};

        $(this.elementsWidget).html('');

		// Format
		// ------
		var objectFormat = {};

		if (input.format.format != null) {
			$.each(input.format.format, function(key, valueFormat) {
				objectFormat[key] = valueFormat;
			});
		}

		// Require empty object
		// --------------------
		if (object == null || typeof(object) != "object") object = {};

		// Для каждой записи пытаемся сгенерировать поля правильного типа
		// ---------------------------
		$.each(object, function(valueIndex, value) {

			if (objectFormat[valueIndex] == null) {

			    var inputFormat = {};

				// Если это массив
				// -------------------
				if (value instanceof Array) inputFormat = {'id' : valueIndex, 'type' : 'list'};

				// Если это smart-значение
				// -------------------
				else if (typeof(value) == "object" && value.smart == true) inputFormat = {'id' : valueIndex, 'type' : 'smartValue'};

				// Если это другие варианты
				// -------------------
				else {
					switch (typeof(value)) {
						case "boolean": inputFormat = {'id' : valueIndex, 'type' : 'boolean'}; break;
						case "triState": inputFormat = {'id' : valueIndex, 'type' : 'triState'}; break;
						case "string": inputFormat = {'id' : valueIndex, 'type' : 'text'}; break;
						case "number": inputFormat = {'id' : valueIndex, 'type' : 'number'}; break;
						case "object": inputFormat = {'id' : valueIndex, 'type' : 'record', 'extendable' : true}; break;
					}
				}

				objectFormat[valueIndex] = inputFormat;
			}

		});


		// Create fields
		// -------------
		$.each(objectFormat, function(inputKey, inputFormat) {
			inputFormat.id = inputKey;
			input.addChild({ 'format' : inputFormat, 'value' : object[inputKey]});
		});

	},


	// Add new child
	// -------------
	addChild : function(args) {

		var input = this;

		// Create record element
		// ---------------------
		var element = new UI.FormInputs.RecordElement(args);

		// Append to elements widget
		// -------------------------
		$(this.elementsWidget).append(element.widget);

		// Append to inputs
		// ---------------
		this.inputs[args.format.id] = element;

	},

	// Remove child element
	// --------------------
	removeChild : function(childKey) {
		if (this.inputs[childKey] != null) {
			$(this.inputs[childKey].widget).remove();
			delete this.inputs[childKey].input;
			delete this.inputs[childKey];
		}
	},

	// Get value
	// ---------
	getValue : function() {
		var data = {};
		for (var inputIndex in this.inputs) {
			var value  = this.inputs[inputIndex].input.getValue();
			if (value != null) data[inputIndex] = value;
		}

		// Len
		// ---
		var len = function(obj) {
		    var L = 0;
		    $.each(obj, function(i, elem) { L++; });
		    return L;
		};

		// Return null
		// -----------
		if (len(data) == 0) return null;

		return data;
	}
});


// Record element
// --------------
UI.FormInputs.RecordElement = function(args) {

	// Create widget
	// -------------
	this.widget = $('<div class="record-element"> \
		<div class="element-key"> \
			<div class="title"></div> \
			<div class="key"></div> \
		</div> \
		<div class="element-body"></div> \
	</div>');

	// Init events
	// -----------
	Events.call(this);

	// Options
	// -------
    args.format.showKey = safeAssign(args.format.showKey, true);

    this.format = args.format;
	this.id = args.format.id;
	this.value = args.value;

	// Create input
	// ------------
	this.input = UI.Form.prototype.getInput(args);
	$(this.input.widget).appendTo($(this.widget).children('.element-body'));

	// Add key and title
	// -----------------
	if (args.format.id != null && args.format.showKey == true) $(this.widget).find("> .element-key .key").html(args.format.id);
	if (args.format.title != null) $(this.widget).find("> .element-key .title").html(args.format.title);

}

// Prototype
// ---------
UI.FormInputs.RecordElement.prototype = $.extend({}, Events.prototype, {

	// Get input preview value
	// -----------------------
	getPreviewValue: function() {
		return this.input.getPreviewValue();
	}
});


// Record element editor
// ---------------------
RecordElementEditor = function(args) {

    // Init events
    // -----------
    var editor = this;
    Events.init(this);

    // Create window
    // -------------
    this.window = new Flex.Window({ 'title' : 'Редактирование значения', 'modal' : true, 'maximizable' : true, 'class' : ['record-element-editor','adminTools'], 'width' : 600, 'minHeight' : 200});

    // Create form
    // -----------
    this.form = new UI.Form({
        'format' : args.format,
        'object' : args.value
    });

    // Add window toolbar
    // ------------------
    var toolbar = this.window.windowToolbar;
    toolbar.clear();

    // Complete button
    // ---------------
    toolbar.addButton({
        'id' : 'save',
        'title' : 'Сохранить',
        'click' : function() {

            // Submit event
            // ------------
            editor.callEvent('complete', editor.form.getValue());

            // Close window
            // ------------
            editor.window.close();
        }
    });

    // Add form
    // --------
    $(this.window.widget).append(this.form.form);
}

// Editor prototype
// ----------------
RecordElementEditor.prototype = $.extend({}, Events.prototype, {});
