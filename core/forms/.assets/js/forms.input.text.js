// Text
// ========================================================================
UI.FormInputs.text = function(args) {

    if (args == null) args = {};

	var input = this;
	UI.FormInput.call(this, args);

	// Detect field type
	// -----------------
	if (args != null && args.format != null && args.format.isPassword == true) {
        this.type = 'password';
    } else  {
        this.type = 'text';
    }

	// Frame
	// -----
	this.input = $('<input type="' + this.type + '"/>');
    this.widget = safeAssign(args.widget, '<div></div>');
	this.widget = $(this.widget).append(this.input);
	this.initWidget();

	// Change event
	// ------------
	$(this.input).change(function() {
		input.callEvent('change', input.getValue());

	});

	// Set value
	// ---------
	if (args.value != null) {
		this.setValue(args.value);
	}
	// ID
	// --
	if (args.format.id != null) $(this.input).attr('name', args.format.id);
	if (args.format.placeholder != null) $(this.input).attr('placeholder', args.format.placeholder);
    if (args.format.autocomplete != null) $(this.input).attr('autocomplete', args.format.autocomplete);

};


UI.FormInputs.text.prototype = $.extend({}, UI.FormInput.prototype, {

	// Get input value
	// ---------------
	getValue : function() {
		var value = $(this.input).val();
		if (value == "") return null;
		return value;
	},

	// Get preview value
	// -----------------
	getPreviewValue: function() {
		var value = this.getValue();
		if (value == null) return null;

		value = value.replace(/(<([^>]+)>)/ig,"");
		if (value.length > 50) value = value.substr(0, 50);

		return value;
	},

	// Set value
	// ---------
	setValue: function(value) {
		this.value = value;
		$(this.input).val(value);
	}

});
