// HTML
// ========================================================================
UI.FormInputs.html = function(args) {
	UI.FormInput.call(this, args);
	this.widget = $('<span>' + args.format.html + '</span>');

	this.initWidget();
};
UI.FormInputs.html.prototype = $.extend({}, UI.FormInput.prototype);
