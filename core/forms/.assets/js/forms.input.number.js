// Number
// ========================================================================
UI.FormInputs.number = function(args) {

	UI.FormInput.call(this, args);
	this.widget = $('<input type="text" />');
	this.initWidget();
	if (args.value != null && args.value != undefined) $(this.widget).val(args.value);


};
UI.FormInputs.number.prototype = $.extend({}, UI.FormInput.prototype, {

	// Чтение значения из числового поля
	// -----------------------------
	getValue : function() {

		var value = $(this.widget).val();

		// Если ничего нет, ничего не выдаем
		// -------------------------
		if (value == "") {
			return null;
		}

		// Преобразование в число
		// -------------------------
		value = Number(value);
		if (!isNaN(value)) return value;

		// Возвращаем неопределенное значение
		// -------------------------
		return null;
	},

	// Get preview value
	// -----------------
	getPreviewValue: function() {
		return this.getValue();
	}

});

