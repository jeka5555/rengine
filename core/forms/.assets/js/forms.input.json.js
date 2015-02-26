// JSON
// ========================================================================
UI.FormInputs.json = function(args) {
	UI.FormInput.call(this, args);
	this.widget = $('<textarea></textarea>');
	this.initWidget();
};
UI.FormInputs.json.prototype = $.extend({}, UI.FormInput.prototype, {

	// Чтение значения
	// -------------------------------
	getValue : function() {
		value = $(this.widget).val();
		value = $.parseJSON(value);
		return value;
	}
});
