UI.FormInputs.radioBoolean = function(args) {

	//This
	// ---
	var input = this;

	UI.FormInput.call(this, args);

	var id = 'bl' + Math.round(Math.random()*1000000);


	// Widget
	// ------
	this.widget = $('<div></div>');

	var trueLabelText = safeAssign(this.format.trueLabel, 'да');
	var trueLabel = $('<label>' + trueLabelText + '</label>');
	this.trueInput = $('<input type="radio" name="' + id + '"/ >').data('value', true).prependTo(trueLabel);

	var falseLabelText = safeAssign(this.format.falseLabel, 'нет');
	var falseLabel = $('<label>' + falseLabelText + '</label>');
	this.trueInput = $('<input type="radio" name="' + id + '"/ >').data('value', false).prependTo(falseLabel);


	this.trueWidget = $('<div class="radio-option"/>').append(trueLabel);
	this.falseWidget = $('<div class="radio-option"/>').append(falseLabel);


	// Append
	// ------
	$(this.widget).append(this.trueWidget).append(this.falseWidget);

	$(this.widget).change(function() {
		input.callEvent('change', $(input.widget).is(":checked"));
	});

	this.initWidget();

};


UI.FormInputs.radioBoolean.prototype = $.extend({}, UI.FormInput.prototype, {


	// Set value
	// ---------
	setValue : function(value) {
		this.value = value;
		if (this.value == true) $(this.trueInput).prop("checked", true);
		else if (this.value == false) $(this.falseInput).prop("checked", true);
	},

	// Get value
	// ---------
	getValue : function() {
		var value = null;
		if ($(this.trueInput).prop("checked")) value = true;
		else if ($(this.falseInput).prop("checked")) value = false;
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

