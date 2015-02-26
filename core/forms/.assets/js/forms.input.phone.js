UI.FormInputs.phone = function(args) {

    if (args == null) args = {};

	var input = this;
	UI.FormInput.call(this, args);

	// Frame
	// -----
	this.codeInput = $('<input type="text"/>');
	this.numberInput = $('<input type="text"/>');


	// Create widget
	// -------------
	this.widget = $('<div>' +
		'<span class="phone-code"><span>Код:</span></span>' +
		'<span class="phone-number"><span>Номер:</span></span>' +
	'</div>');

	$(this.widget).find(".phone-code").append(this.codeInput);
	$(this.widget).find(".phone-number").append(this.numberInput);
	this.initWidget();

	// Set value
	// ---------
	if (args.value != null) {
		this.setValue(args.value);
	}

};


UI.FormInputs.phone.prototype = $.extend({}, UI.FormInput.prototype, {

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
