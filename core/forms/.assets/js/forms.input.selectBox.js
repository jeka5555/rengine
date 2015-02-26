// Мультиселект
// =======================================================================
UI.FormInputs.selectBox = function(args) {

	var input = this;	
  UI.FormInput.call(this, args);

	if (this.value == null) this.value = [];

	this.widget = $('<div> \
			<span class="values"></span> \
			<input class="select" type="button" value="выбрать"/> \
	</div>');
	this.initWidget();

	// Загрузка формата
	// ----------------
	if (this.format.source != null) {
		API.action({
			'action' : this.format.source,
			'callback' : function(result) {				
				input.values = result;
				input.init();
			}
		});
	}
	// Если значения преданы в аргументах
	// ----------------
	else if(this.format.values != null) input.values = this.format.values; 
	                                     
	// Виджет для значений
	// -------------------
	this.valuesWidget = $(this.widget).find('.values');

	// Привязка выбора к кнопке
	// ------------------------
	$(this.widget).find('input').click(function() {
		input.openSelect();
	});


};

UI.FormInputs.selectBox.prototype = $.extend({}, UI.FormInput.prototype, {

	// Инициализация значений
	// ---------------------- 
	init : function() {
		
		var input = this;
		       
		if (this.values != null) {			
			if (this.value != null) {

				$(input.valuesWidget).empty();

				// Обновление
				// ----------			
				if (input.value != null) {

					// Сборка значений во временный контейнер
					// --------------------------------------
					var content = [];
					$.each(input.value, function(valueIndex, val) {				
							content.push('<span class="input-selectBox-value">' + input.values[val].title + '</span>');
					});

					$(input.valuesWidget).append(content.join(", "));

				}
			}
		}
	},
	
	openSelect : function() {

		var window;	
		var content = $('<div></div>');
		var input = this;

		var form = $('<div class="selectBox-form"></div>').appendTo(content);
		var buttonsWidget = $('<div class="selectBox-buttons"></div>').appendTo(content);

		// Вставка варианатов
		// ------------------
		if (input.values != null) {
			$.each(input.values, function(key, value) {
				var checkinput = $('<div class="input-selectBox-item"></div>');			
				var checkbox = $('<label><input name="' + key + '" type="checkbox"> <span>' + value.title + '</span></label>').appendTo(checkinput);
         
				if (input.value.indexOf(key) != -1) $(checkbox).find('input').prop('checked', true);

				$(form).append(checkinput);
			});
		}
		                                       

		// Добавляем кнопку выбора
		// -----------------------
		var button = $('<input type="button" value="Готово"/>').appendTo(buttonsWidget);
		$(button).click(function() {

			value = [];
			$(form).find(".input-selectBox-item input").each(function() {
				if ($(this).prop('checked') == true) value.push($(this).attr('name'));
			}); 
			
			input.value = value;
			input.init();
			window.close();
		});

		// Создаем окно
		// ------------
		var window = new Flex.Window({'title' : this.format.title, 'content' : content, 'appendTo' : 'body'});
	},

	// Чтение значения
	// ----------------------------------
	getValue : function() {
		return this.value;
	}

});
