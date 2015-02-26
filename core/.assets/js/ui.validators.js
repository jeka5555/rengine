UI.Validators = {

	// Проверка в соотвествии с ссылкой
	// ----------------
	'uri' : function(value, validator) {},

	// Общий валидатор
	// ----------------
	'generic' : function(value, validator) {},

	// Крутой пароль
	// ----------------
	'strongPassword' : function(value) {
		var errors = [];

		// Нужна строка
		// ------------
		if (typeof(value) != "string") {
			errors.push({'text' : 'Пароль должен быть строкой'});
			return errors;
		}

		// Длина не менее 6 символов
		// ------------
		if (value.length < 6) errors.push({'text' : 'Пароль должен быть не менее 6 символов в длину'});
		
		// Отдаем ошибки
		// ------------
		if (errors.length > 0) return errors;
		return true;
	},

	// Значение должно быть указано
	// ----------------
	'required' : function(value) {
		if (value == null || value == "") return [{'text' : 'Поле не должно быть пустым!'}];
		return true;
	},

	// Проверка по регулярному выражеию
	// -------------------
	'regexp' : function(value, validator) {

		// Валидатор должн быть корреткным
		// ---------------
		if (validator == null || validator.regexp == null) return [{'text' : 'Не указан шаблон сопадения'}];

		// Генерация регулярного выражения
		// ---------------
		var regex = new RegExp(validator.regexp, 'ig');
		
		// Проверяем
		// ----------------
		var result = regex.test(value);
		if (!result) return [{'text' : 'Несовпадение с шаблоном'}];

		return true;

	},

	// Емейл
	// ----------------
	'email' : function(value) {
		var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var result = regex.test(value);
		if (!result) return [{'text' : 'Значение имеет формат отличный от адреса электронной почты'}];
		return true;
	}

}
