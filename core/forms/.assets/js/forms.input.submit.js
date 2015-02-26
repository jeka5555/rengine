// Submit
// ========================================================================
UI.FormInputs.submit = function(args) {

	var input = this;
	UI.FormInput.call(this, args);

	this.widget = $('<input type="submit" />');
	this.initWidget();
	if (args.format.title != undefined) $(this.widget).attr("value", args.format.title);
	
	if (args.format.disabled == true) $(this.widget).attr("disabled", "disabled");
};

UI.FormInputs.submit.prototype = $.extend({}, UI.FormInput.prototype);
