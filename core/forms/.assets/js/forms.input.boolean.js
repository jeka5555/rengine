// Boolean
// ========================================================================
UI.FormInputs.boolean = function(args) {

	//This
	// ---
	var input = this;

	UI.FormInput.call(this, args);

	this.widget = $('<input type="checkbox" name="'+args.id+'" />');
	if (this.value != null)	$(this.widget).prop("checked", this.value);

	$(this.widget).change(function() {
		input.callEvent('change', $(input.widget).is(":checked"));
	});

	this.initWidget();

};

UI.FormInputs.boolean.prototype = $.extend({}, UI.FormInput.prototype, {


	// Get value
	// ---------
	getValue : function() {
		var value = $(this.widget).prop("checked");
		return value;
	},

	// Get preview value
	// -----------------
	getPreviewValue: function() {

		// Get value
		// ---------
		var value = this.getValue();

		// Format it
		// ---------
		if (value == true) return 'да';
		else if (value === false) return 'нет';
		else return 'null';
	}
});

