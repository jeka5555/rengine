// Дата и время
// =======================================================================
UI.FormInputs.datetime = function(args) {

	var input = this;
  	UI.FormInput.call(this, args);

	this.widget = $('<div></div>');

	// Создаем инпут для даты
	// --------------------------
	if(args.format.showDate !== false) {
		this.dateInput = $('<input type="text" class="date"/>').appendTo(this.widget);

		this.calendar = $('<span class="calendar"></span>');
		$(this.widget).append(this.calendar);
	}

	// Создаем инпут для времени
	// --------------------------
	if(args.format.showTime !== false) {
		this.timeInput = $('<input type="text" class="time" />').appendTo(this.widget);
	}

	var date = new Date();
	// Если значение задано, то берем его
	// ---------------------
	if (args.value != null) date = new Date(Number(args.value)*1000);

	// Заполняем значения полей
	// ---------------------
	function append(num) { return (num < 10 ? '0' : '') + num; }


    if (args.format.empty !== true || args.value != null) {
        $(this.dateInput).val(date.getDate() + '.' + append(date.getMonth()+1) + '.' + append(date.getFullYear()));
        $(this.timeInput).val(append(date.getHours()) + ':' + append(date.getMinutes()));
    }

	$(this.dateInput).datepicker({
		dateFormat: 'dd.mm.yy',
		changeMonth: true,
		changeYear: true,
		yearRange: "-100:+0"
	});


	// Add calendar
	// ------------
	if (this.calendar != null) {
		$(this.calendar).click(function() {
			$(input.dateInput).datepicker('show');
		})
	}

	this.initWidget();

};

UI.FormInputs.datetime.prototype = $.extend({}, UI.FormInput.prototype, {

	// Чтение значения
	// ----------------------
	getValue : function() {
		var data = $(this.dateInput).val();
		var time = $(this.timeInput).val()?$(this.timeInput).val():'00:00';
		var dataParts = data.split(".");
		var timeParts = time.split(":");

		var unix = Number(new Date(dataParts[2], (dataParts[1] - 1), dataParts[0], timeParts[0], timeParts[1]))/1000;
		return unix;
	}
});
