// Generic
// ========================================================================
UI.FormInputs.generic = function(args) {
	UI.FormInput.call(this, args);
	this.widget = $('<div>...</div>');

	this.initWidget();
};
UI.FormInputs.generic.prototype = $.extend({}, UI.FormInput.prototype);
