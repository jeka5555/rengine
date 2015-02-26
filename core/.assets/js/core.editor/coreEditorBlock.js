
// Editor block
// ------------
CoreEditorBlock = function(args) {

	// Init
	// ----
	CoreEditorElement.call(this, args);

	// Create widget
	// -------------
	this.widget = $('<div class="core-editor-element form-block" />');
	this.update(args);


};

CoreEditorBlock.prototype = $.extend({}, CoreEditorElement.prototype, {

	// Update block content
	// --------------------
	update : function(args) {

		var coreEditorBlock = this;

		// Options
		// -------
		this.closeable = safeAssign(args.closeable, true);
		this.closed = safeAssign(args.closed, false);
		this.title = args.title;
		if (args.closed == true) this.closeable = true;


		// Placeholder to keep elements
		// ----------------------------
		this.elements = {};

		// Create elements
		// ---------------
		if (args.elements != null)
		$.each(args.elements, function(elementIndex, elementData) {

			// Pass properties
			// ---------------
			elementData.editorProperties = args.editorProperties;
			elementData.editor = args.editor;

			// Create element
			// --------------
			var element = CoreEditorElement.createElement(elementData);
			coreEditorBlock.elements[elementIndex] = element;
		});

	},

	// Update widget
	// -------------
	updateWidget : function() {


		$(this.widget).empty();

		// This
		// ----
		var coreEditorBlock = this;

		// Если есть название
		// ------------------
		if (this.title != null) {
			this.titleWidget = $('<div class="form-block-title"></div>');
			$(this.titleWidget).append('<span class="icon"/>');
			$(this.titleWidget).append('<span class="title">' + this.title + '</span>');
			$(this.titleWidget).appendTo(this.widget);
		}

		// Close events
		// ------------
		if (this.closeable) {
			$(this.widget).addClass('closeable');
			$(this.titleWidget).click(function() {
				coreEditorBlock.toggle();
			})
		}

		// Close by default
		// ----------------
		if (this.closeable && this.closed) {
			this.close();
		}

		// Content widget
		// --------------
		this.contentWidget = $('<div class="form-block-content"></div>').appendTo(this.widget);

		// Add elements
		// ------------
		if (this.elements != null)
		$.each(this.elements, function(elementIndex, elementData) {
			elementData.updateWidget();
			$(coreEditorBlock.contentWidget).append(elementData.widget);
		});

	},

	// Toggle visibility
	// -----------------
	toggle: function() {
		if (this.closed) this.open();
		else this.close();
	},

	// Close block
	// ------------
	close: function() {
		this.closed = true;
		$(this.widget).addClass('closed');
	},

	// Open block
	// ----------
	open: function() {
		this.closed = false;
		$(this.widget).removeClass('closed');
	}


});
