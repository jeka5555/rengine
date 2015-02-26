UI.FormInputs.component = function(args) {

	// Init inpute
	// -----------
	UI.FormInput.call(this, args);

	// Create widget
	// -------------
	this.widget = $('<div></div>');
	this.typeWidget = $('<div class="type-selector" />').appendTo(this.widget);
	this.idWidget = $('<div class="id-selector" />').appendTo(this.widget);

	// Init widget
	// -----------
	this.initWidget();

	// Init data
	// ---------
	this.init(args);

};


UI.FormInputs.component.prototype = $.extend({}, UI.FormInput.prototype, {

	// Init widget
	// -----------
	init : function() {

		// This
		// ----
		input = this;

		// Empty widget
		// ------------
		$(this.typeWidget).empty();

		// Set full value
		// --------------
		if (this.value != null && this.format.componentType != null) {
			this.value = {'type' : this.format.componentType, 'id' : this.value};
		}

		// Correct value format
		// --------------------
		if (this.value == null) this.value = {'type' : null, 'value' : null};

		// If component type not defined
		// -----------------------------
		if (this.format.componentType == null) {

			// Request component types
			// -----------------------
			API.action({
				'action' : '/module/components/getComponentTypes',
				'callback' : function(data) {

					// List of components
					// ------------------
					input.componentTypes = data;

					// Create input
					// ------------
					var selectTypeInput = new UI.FormInputs.select({'format' : {
						'values' : input.componentTypes,
						'allowEmpty' : true,
						'value' : input.value.type
					}});
					$(input.typeWidget).append(selectTypeInput.widget);

					// Add event
					// ---------
					selectTypeInput.addListener('change', function(e) {
						input.changeType(e.data);
					});
				}
			});

		}

		// Component type is given
		// -----------------------
		else {
			this.changeType(this.format.componentType);
		}


	},

	// Change component type
	// ---------------------
	changeType : function(type) {


		// Guards
		// ------
		if (type == null) return;

		// This
		// ----
		var input = this;

		// Set type
		// --------
		input.value.type = type;

		// Clear container
		// ---------------
		$(input.idWidget).empty();

		// Load components list
		// --------------------
		API.action({
			'action' : '/module/components/getComponentsList',
			'data' : input.value.type,
			'callback' : function(data) {

					// Create select
					// -------------
					var selectComponentInput = new UI.FormInputs.select({
						'format' : { 'values' : data, 'allowEmpty' : true},
						'value' : input.value.id
					});
					$(input.idWidget).append(selectComponentInput.widget);

					// Add event
					// ---------
					selectComponentInput.addListener('change', function(e) {
						input.value.id = e.data;
						input.callEvent('change', input.value);
					});
				}
		});

	},

	// Get value
	// ---------
	getValue : function() {

		// For null
		// --------
		if (this.value == null || this.value.type == null) return null;

		// If type exists
		// --------------
		if (this.format.componentType != null) return this.value.id;

		// Or return a full component definition
		// -------------------------------------
		return this.value;
	},

	// Set value
	// ---------
	setValue: function(value) {
		this.value = value;
		this.init();
	}

});
