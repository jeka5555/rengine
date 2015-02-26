
// Simple preview widget
// ---------------------
Component.register({
	'type'  : 'widget',
	'id' : 'advancedSearchPreview',
	'title' : 'Предварительный просмоттр результатов',

	// Constructor
	// -----------
	'constructor' : function(args) {
		this.widget = $('<div class="advanced-search-preview"></div>');
		if (args.value != null) $(this.widget).html(args.value);
	}

});


Component.register({

	// Create component
	// ----------------
	'type' : 'input',
	'id' : 'advancedSelect',
	'title' : 'Расширенное поле выбора',

	// Constructor
	// -----------
	'constructor' : function(args) {


		var input = this;

		// Options
		// -------
		this.valueWidgetID = 'advancedSearchPreview';
		this.previewWidgetID = 'advancedSearchPreview';

		// Values
		// ------
		this.values = {
			'apple' : 'Яблоко',
			'pear' : 'Груша',
			'bannana' : 'Банан'
		};

		this.valuesSourceURI = this.format.valuesSourceURI;
		this.value = null;

		// Interaction
		// -----------
		this.openOnClick = safeAssign(this.format.openOnClick, true); // Allow open by click
		this.openOnType = safeAssign(this.format.openOnType, true); // Allow open by type

		// User input
		// ----------
		this.userInputEnabled = safeAssign(this.format.userInputEnabled, true);

		// Data source
		// -----------
		this.queryEnabled = safeAssign(this.format.queryEnabled, true); // Enable server query mode
		this.querySourceURI = this.format.querySourceURI; // URI from which we load query results
		this.queryUpdateTimeout = 200; // Wait time, used when user type new query


		// Create widget
		// -------------
		this.widget = $('<div>' +
			'<div class="select-box-container">' +
				'<span class="value"></span>' +
				'<span class="divider"></span>' +
				'<span class="select-button"></span>' +
			'</div>' +
			'<div class="values-container"></div>' +
		'</div>');

		// Subwidgets
		// ----------
		this.selectButton = $(this.widget).find(".select-button");
		this.valueWidget = $(this.widget).find(".value");
		this.valuesWidget = $(this.widget).find(".values-container").hide();

		// Button
		// ------
		if (this.openOnClick == true) {
			$(this.selectButton).click(function() {
				input.openDropDown();
			});
		}


		this.setValue('ferfe');

	},

	// Set value
	// ---------
	'setValue' : function(value) {
		this.value = value;
		this.updateValueWidget();
	},


	// Close
	// -----
	'closeDropDown' : function() {
		$(this.valuesWidget).hide();
	},


	// Open drop down
	// --------------
	'openDropDown' : function() {

		// This
		// ----
		var input = this;

		// Clear
		// -----
		$(this.valuesWidget).empty();

		// Get preview widget component
		// ----------------------------
		var previewComponent = Components.get('widget', this.previewWidgetID);

		// Convert each item to visual representation
		// -------------------------------------------
		$.each(this.values, function(valueIndex, value) {
			var widget = new previewComponent.constructor({'value' : value});

						// Listen
			// ------
			$(widget.widget).click(function() {
				input.setValue(valueIndex);
				input.closeDropDown();
			});

			// Append class and add to widget
			// ------------------------------
			$(widget.widget).addClass('advancedSelect-value');
			$(input.valuesWidget).append(widget.widget);
		});

		// Show widget
		// -----------
		$(this.valuesWidget).css({
			'z-index' : '10000',
			'min-width' : $(this.widget).children(".select-box-container").width()
		}).show();
	},

	// Update walue
	// ------------
	'updateValueWidget' : function() {

		$(this.valueWidget).empty();

		// Construct value widget
		// ----------------------
		var widgetComponent = Components.get('widget', this.valueWidgetID);
		var widget = new widgetComponent.constructor({'value' : this.value});

		// Append
		// ------
		$(this.valueWidget).append(widget.widget);
	}



});



// Number
// ========================================================================
UI.FormInputs.advancedSelect = function(args) {

	UI.FormInput.call(this, args);

	// Init component
	// --------------
	var component = Components.get('input', 'advancedSelect');
	component.constructor.call(this, args);

	this.initWidget();
};

UI.FormInputs.advancedSelect.prototype = $.extend({}, UI.FormInput.prototype, Components.get('input', 'advancedSelect').prototype);

