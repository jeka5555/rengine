// Таблица для вывода данных
// ----------------------------------
Flex.APITable = function(args) {

	Events.call(this);
	this.widget = $('<div class="flex-data-table"></div>');
	this.data = args.data;
	this.parsers = args.parsers;
	var table = this;

	// Собираем заголовок
	// -------------------------------
	this.heading = $('<div class="flex-table-heading"></div>').appendTo(this.widget);

	$.each(this.data.fields, function(fieldIndex, fieldData) {
		var cell = $('<div>' + fieldData.title + '</div>');

		// Элемент сортировки
		// ----------------------------
		if (fieldData.sortable == true) {
			var sort = $('<span class="flex-sort"><span class="up">+</span> <span class="down">-</span></span>')
			$(cell).append(sort);

			// Присоединяем события
			// ------------------------
			$(sort).children('.up').click(function() { table.callEvent('sort', {'field' : fieldData.id, 'direction' : 1}); })
			$(sort).children('.down').click(function() { table.callEvent('sort', {'field' : fieldData.id, 'direction' : -1}); })

		}

		$(cell).appendTo(table.heading);
	});


	// Собираем строки
	// --------------------------------
	$.each(this.data.objects, function(objectIndex, objectData) {
		table.addRow(objectData);
	});
	

}

Flex.APITable.prototype = $.extend({}, Events.prototype, {

	// Добавляем линию
	// -------------------------------
	addRow : function(rowData) {
		var table = this;
	
		var row = $('<div class="flex-table-row"></div>');

		// Строим ячейки таблицы
		// ----------------------------
		var cellNumber = 0;
		$.each(rowData.data, function(cellIndex, cellValue) {
			var cell =  $('<div>' + cellValue.data + '</div>').appendTo(row);

			if (cellValue.meta != null && table.parsers[cellValue.meta.type] != null) table.parsers[cellValue.meta.type](cellValue.meta, cellValue.data, cell);

			// Опции для форматирования
			// ------------------------
			if (table.data.fields[cellNumber] != null) {
				if (table.data.fields[cellNumber]['text-align'] != null) $(cell).css({'text-align' : table.data.fields[cellNumber]['text-align']});
				if (table.data.fields[cellNumber]['width'] != null) $(cell).css({'width' : table.data.fields[cellNumber]['width']});
			}

			cellNumber++;
		});

		// Вызов парсера
		// -------------------
		if (rowData.meta != null) {
			if (table.parsers[rowData.meta.type] != null) table.parsers[rowData.meta.type](rowData.meta, rowData.data, row);
		}

		$(row).appendTo(table.widget);
	}

});
