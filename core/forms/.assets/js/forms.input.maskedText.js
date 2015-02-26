// maskedText
// ========================================================================
UI.FormInputs.maskedText = function(args) {
	
	UI.FormInput.call(this, args);
	var maskedText = this;

    maskedText.widget = safeAssign(args.widget, $('<div class="form-input-maskedText"></div>'));

	if (args.format.format != null)
		// Строим по формату вывод
		// -----------------------------
		$.each(args.format.format, function(index, data) {
			if($.type( data ) === "string")		
				// Если строка, то простой span
				$(maskedText.widget).append('<span>'+data+'</span>');
				
			else if(data.content != null) {
				// Если есть контент, то span
				var element = $('<span>'+data.content+'</span>').appendTo(maskedText.widget); 
			  if(data.class != null) $(element).addClass(data.class);
				 
			} else {
				// Инача строим инпут
				var element = $('<input type="' + args.format.type + '"/>').appendTo(maskedText.widget); 
				           	
				// Атрибуты и опции
			  if(data.class != null) $(element).addClass(data.class);
			  if(data.mask != null) $(element).attr('placeholder', data.mask);
			  if(data.maxLength != null) $(element).attr('maxlength', data.maxLength);
			  if(data.minWidth != null) {
					$(element).data('min-width', data.minWidth);
					$(element).width(data.minWidth);
				} else
					 $(element).css('width', 'auto');	
					 
				// Восстанавливаем значния, если есть
				if(args.value != null && args.value[index] != null) {
					$(element).val(args.value[index]);
				} 
				

				// Прослушиваем нажатия на инпут
				$(element).bind("keyup", function(e){ 

					var keyCode = e.which;

          if (keyCode == 8 && this.value.length == 0)
            $(this).prevAll('input:first').focus();
					else if (e.ctrlKey || e.altKey || e.metaKey)
						// Игнорируем
						return;
					else if (keyCode) {
						// Преобразуем код в символ
						var word = String.fromCharCode(keyCode);
						
						// Динамическая ширина инпута
						var spanWidth = $('<span>'+$(this).val()+'</span>').css('font', $(element).css('font')).appendTo('body').hide().width() + 2;
						$(spanWidth).remove();
						
						// Подсветка правильности ввода
						if(spanWidth > $(element).data('min-width'))
							$(element).width(spanWidth);
						else
							$(element).width($(element).data('min-width'));
						  
						$(element).removeClass('ok error');	   
						
						// Авто перереход на следующий инпут
						if(data.maxLength != null && $(this).val().length >= data.maxLength)
							$(this).nextAll('input:first').focus();	

						// Соответсвие регулярному выражению
						if(new RegExp(data.regexp).test($(this).val())) {
							$(element).addClass('ok');
							return true;						
						} else {
							$(element).addClass('error');
							return false;
						}	
										
					}
				})
			}
		});

    if (args.format.preValidate != null) {
        var preValidateButton = $('<div class="preValidateButton"></div>');

        $(preValidateButton).click(function(){
            $(maskedText.widget).spin(spinOptsGreen);
            $(preValidateButton).attr('disabled', 'disabled');
            API.action({
                'action' : args.format.preValidate,
                'data' : {'value' : maskedText.getValue() },
                'callback' : function(result) {
                    $(maskedText.widget).spin(false);
                    $(preValidateButton).attr('disabled', false);
                }
            });
        });

        $(preValidateButton).appendTo(maskedText.widget);
    }

};


UI.FormInputs.maskedText.prototype = $.extend({}, UI.FormInput.prototype, {

	// Проверка значения
	// ---------------------------
	validate : function() {
		this.clearErrors();
		return true;
	},

	// Получаем значение
	// ---------------------------
	getValue : function() {
	
		var value = [];
		
		$.each($(this.widget).children(), function(index, element) {
			if($(element).prop("tagName") == 'INPUT') value.push($(element).val());
			else if($(element).prop("tagName") == 'SPAN') value.push($(element).text());
		});
		
		return value;
	}
	
});