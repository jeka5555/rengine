// Button
// ========================================================================
UI.FormInputs.button = function(args) {
	var input = this;
	UI.FormInput.call(this, args);

	this.widget = $('<input type="button" />');

	if (args.format.title != undefined) $(this.widget).attr("value", args.format.title);

	// Событие клика
	// -----------------------------
	$(this.widget).click(function() { input.callEvent('click'); });

	this.initWidget();
	
};
UI.FormInputs.button.prototype = $.extend({}, UI.FormInput.prototype);
