UI.FormInputs.radio = function(args) {

	// This
	// ----
	var input = this;
	UI.FormInput.call(this, args);

	// Create widget
	// -------------
	this.widget = $('<div></div>');
	this.initWidget();

	var inputID = 'radio-' + Math.round(Math.random() * 10000000);

	// Add values
	// ----------
	if (this.format.values != null) {

		$.each(this.format.values, function(valIndex, val) {

			// Create options box
			// ------------------
			var optionBox = $('<div class="radio-option"></div>');

			// Add  label
			// ----------
			var label = $('<label>' + val + '</label>').appendTo(optionBox);
			var option = $('<input type="radio" value="' + valIndex + '" name="' + inputID + '"/ >').data('value', valIndex).prependTo(label);

			// Set value
			// ---------
			$(option).click(function() {
				input.setValue(valIndex);
			});

			// Add to input
			// ------------
			$(input.widget).append(optionBox);
		});
	}

	// Set value
	// --------
	if (this.value != null) this.setValue(this.value);
};

UI.FormInputs.radio.prototype = $.extend({}, UI.FormInput.prototype, {

	// Set value
	// ---------
	setValue : function(val) {

		// Old set
		// -------
		UI.FormInput.prototype.setValue.call(this, val);

		// Switch visual
		// -------------
		$(this.widget).find("input").each(function() {

			// Set
			// ---
			if ($(this).data('value') == val) {
				$(this).prop('checked', true);
			}

			// Unset
			// -----
			else {
				$(this).prop('checked', false);
			}
		});

	},

	// Get value
	// ---------
	getValue : function() {

		var input = this;
		var value = null;

		// Add checked as value
		// --------------------
		$(this.widget).find("input:checked").each(function() {
			value = $(this).data('value');
		});

		return value;
	}

});
