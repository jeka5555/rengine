// Мультиселект
// =======================================================================
UI.FormInputs.multiselect = function(args) {

	var input = this;
  UI.FormInput.call(this, args);

	this.widget = $('<div></div>');
	this.initWidget();

	// Здаем значения
	// ---------------------
	this.inputs = {};
	if (this.value == undefined) this.value = [];

	// Пытаемся отобразить варианты
	// ----------------------------------------
	if (this.format.values != null) {
		for (var valueIndex in this.format.values) {

			var variantInput = $('<div class="multiselect-option"> \
				<label><span class="option"></span> \
				<span class="title"></span></label> \
			</div>');

			// Добавляем название
			// -----------------------------------
			var variantTitle = this.format.values[valueIndex];
			$(variantInput).find(".title").html(variantTitle);

			// Добавляем поле
			// -----------------------------------
			var variantOption = $('<input type="checkbox" />');

			input.inputs[valueIndex] = variantOption;

			if (this.value.indexOf(valueIndex) != -1) $(variantOption).prop("checked", true);

			$(variantInput).find(".option").append(variantOption);
			$(variantInput).appendTo($(this.widget));

		}
	}
};

UI.FormInputs.multiselect.prototype = $.extend({}, UI.FormInput.prototype, {

	// Чтение значения
	// ----------------------------------
	getValue : function() {

		var result = []
		$.each(this.inputs, function(inputIndex, inputWidget) {
			if ($(inputWidget).prop("checked")) result.push(inputIndex);
		});

		return result;
	}

});
