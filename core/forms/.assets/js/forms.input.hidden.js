// Text
// ========================================================================
UI.FormInputs.hidden = function(args) {

	UI.FormInput.call(this, args);
	
	// Конструируем визуальный каркас
	// ---------------------------
	this.input = $('<input type="hidden"/>');
	this.widget = $('<div></div>').append(this.input);

	if (args.value != null) { $(this.input).val(args.value);}
	if (args.format.id != null) $(this.input).attr('name', args.format.id);

	this.initWidget();
};


UI.FormInputs.hidden.prototype = $.extend({}, UI.FormInput.prototype, {

	getValue : function() {
		var value = $(this.input).val();
		if (value == "") return null;
		return value;
	},

	setValue: function(value) {
		this.value = value;
		$(this.input).val(value);
	}

});
