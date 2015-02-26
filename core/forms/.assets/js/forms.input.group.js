// Button
// ========================================================================
UI.FormInputs.group = function(args) {

	var input = this;
	UI.FormInput.call(this, args);

	// Создаем обертку для группы
	// --------------------------
	this.value = safeAssign(this.value, {});
	this.widget = $('<div></div>');
	this.inputs = {};

	// Добавляем все элементы
	// ----------------------
	$.each(this.format.elements, function(elementIndex, element) {
		var subInput = UI.Form.prototype.getInput({ 'format' : element, 'value' : input.value[element.id] });
		$(input.widget).append(subInput.widget);
		input.inputs[element.id] = subInput;
	});

	this.initWidget();
	
};


UI.FormInputs.group.prototype = $.extend({}, UI.FormInput.prototype, {

	getValue : function() {
		data = {};

		// Забираем данные
		// ---------------
		$.each(this.inputs, function(inputIndex, inputWidget) {
			data[inputIndex] = inputWidget.getValue();
		});
	
		// Возврат
		// -------
		return data;
	}
	
});
