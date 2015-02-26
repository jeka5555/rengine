// Выбор объекта
// ===================================================================================================
UI.FormInputs.object = function(args) {

	// Init
	// ----
 	UI.FormInput.call(this, args);
	var input = this;

	// Set options
	// -----------
	this.value = args.value;
	this.addClass = args.format.addClass;
	this.class = input.format.class;
    this.filters = input.format.filters;

	// Widget
	// ------
	this.widget = $('<div class="input-object"></div>');

	// Preview widget
	// --------------
	this.previewWidget = $('<div class="preview"></div>').appendTo(this.widget);

	// Append button
	// -------------
	this.appendButton = $('<input type="button" class="select-object" value = "Выбрать"/>').appendTo(this.widget);

	// Select object
	// -------------
	$(this.widget).find(".select-object").click(function() {

		// Start manager to select objects
		// -------------------------------
		API.action({
			'action' : '/module/objects/pickObject',
			'data' : input.format,
			'callback' : function(result) {

				// Bind selector
				// -------------
				if (result != null) {

					var manager = window[result];

					// Warning
					// -------
					if (manager == null) {
						console.warn('Невозможно опредлить приложение для выбора');
						return;
					}

					// Listen selector
					// ---------------
					manager.addListener('select', function(e) {
						if (e.data == null) return;

						if(e.data[0] != null) {
							input.setValue(e.data[0].id);
						}
					})


				}
			}
		});

	});

	// Drag and drop
	// -------------
	$(this.widget).FlexDroppable({
		'contexts' : {
			'object' : function(data) {

				// Can we do that?
				// ---------------
				if (input.class == data.class) {
					input.setValue(data.id);
				}

			}
		}
	});

	// Update
	// ------
	this.update();


}

UI.FormInputs.object.prototype = $.extend({}, UI.FormInput.prototype, {

	// Get preview value
	// -----------------
	getPreviewValue: function() {

		// It's preview element
		// --------------------
		var valueElement = $('<span class="value"></span>');

		// Request object's identity
		// -------------------------
		API.Objects.action({
			'action' : 'getIdentity',
			'class' : this.class,
			'id' : this.value,

			// Callback
			// --------
			'callback' : function(result) {
				if (result == null) result = 'null';
				$(valueElement).html(result);
			}
		});

		return valueElement;
	},

	// Update view
	// -----------
	update : function() {

		// Init
		// ----
		var input = this;
		$(this.previewWidget).empty();

		// If value is empty, return
		// -------------------------
		if (this.value == null) return;

		// Load preview
		// ------------
		API.Objects.action({
			'action' : 'getPreview',
			'class' : this.class,
			'id' : this.value,
			'callback' : function(data) {


				// If no any data, do nothing
				// --------------------------
				if (data == null) return;

				// Create preview container
				// ------------------------
				var preview = $(data).appendTo(input.previewWidget);

				// Add edit button
				// ---------------
				var editButton = $('<span class="miniButton edit"><span class="icon"></span></span>').appendTo(input.widget).click(function() {
					API.Objects.action({'action' : 'edit', 'class' : input.class, 'id' : input.value});
					Global.addListener('objectUpdate', function(e) {
						if (e.class = input.class && e.id == input.id) input.update();
					});
				}).appendTo(preview);

				// Add delete button
				// -----------------
				var deleteButton = $('<span class="miniButton delete"><span class="icon"></span></span>').appendTo(preview);

				// Delete button event
				// -------------------
				$(deleteButton).click(function() {
					input.setValue(null);
				})

			}
		});
	},

	// Get value
	// ---------
	getValue : function() {
		var input = this;
		return this.value;
	},

	// Set value
	// ---------
	setValue  : function(value) {
		this.value = value;
		this.update();
	}

});
