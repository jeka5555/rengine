// Select
// ========================================================================
UI.FormInputs.select = function(args) {

	UI.FormInput.call(this, args);
	var input = this;

	this.widget = $('<div />');
	this.initWidget();

	this.values = args.format.values;
	this.select = $('<select></select>').appendTo(this.widget);

	if (args.format.name != null) $(this.select).attr('name', args.format.name);

	// Если заданы значения напрямую, забираем
	// ------------------------------------------
	if (args.format.valuesSourceURI == null) {
		this.initValues(args.format.values);
	}

	// Если есть ссылка, загружаем
	// ------------------------------------------
	else {
		API.request({
			'uri' : args.format.valuesSourceURI,
			'callback' : function(values) {
				input.initValues(values);
				input.setValue(input.value);
			}
		});
	}

	// Событие для тех, кому интересно измнение состояния
	// -----------------------------------------
	$(this.select).change(function() {
		input.callEvent('change', input.getValue());
	});

	if (this.value != null) this.setValue(this.value);
};

UI.FormInputs.select.prototype = $.extend({}, UI.FormInput.prototype, {

	// Получаем значение
	// ---------------------------
	getValue : function() {
		var val = $(this.select).val();
		if (val == '') return null;
		else return val;
	},

	// Получаем значение
	// ---------------------------
	setValue : function(value) { 
		this.value = value;
		$(this.select).val(value);
	},

	// Get preview value
	// -----------------
	getPreviewValue: function() {
		var value = this.getValue();
		return this.values[value];
	},

	// Инициализация значений
	// ---------------------------
	initValues : function(values) {


		var input = this;
		$(input.select).empty();

		// Empty option
		// ------------
		if (input.format.allowEmpty == true) {
			var emptyText = '-не выбрано';
			if (input.format.emptyText != null) emptyText = input.format.emptyText;
			$(input.select).append('<option value="">' + emptyText + '</option>');
		}

		// Add values
		// ----------
		if (values != null)
		$.each(values, function(index, value) {
			var optionWidget = $('<option></option>').html(value).val(index);
			$(input.select).append(optionWidget);
		});
	}
});
