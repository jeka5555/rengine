UI.Validator = {

	// Проверка значения в программе валидации
	// ----------------
	validate : function(value, validator) {
		
		var validatorResult = true;
		var errors = [];

		$.each(validator, function(validatorIndex, validatorData) {
			var result = UI.Validator.validateOne(value, validatorData);
			if (result != true) {

				// Если формат ответа неизвестен
				// -------------
				if (typeof(result) != "object") errors.push({'text' : 'Неизвестная ошибка'});

				// Добавляем в ошибки
				// -------------
				else $.each(result, function(errorIndex, error) {
					errors.push(error);
				});
			}
		});

		// Если есть ошибки, присылаем список, иначе true
		// -------------
		if (errors.length > 0) return errors;
		else return true;

	},

	// Метод проверки
	// ----------------
	validateOne : function(value, validator) {

		var validatorID = null;
		var validatorResult = true;
		
		// Получаем идентификатор валидатора
		// ------------
		if (typeof(validator) == "string") validatorID = validator;
		if (typeof(validator) == "object" && validator.type != null) validatorID = validator.type;

		// Если массив, передаем в тип
		// -------------
		if (validatorID != null) {

			// Выполнение валидатора
			// ----------
			var result = UI.Validators[validatorID](value, validator);

			// Если не true, то забираем результат как ошибки
			// ----------
			if (result != true) {

				validatorResult = result;

				// Если в поле валидатора есть дополнительные ошибки, добавлям
				// ----------
				if (validator.errors != null) {
					if (validator.useDefault == false) validatorResult = [].concat(validator.errors);
					else validatorResult = validatorResult.concat(validator.errors);
				} 


			}


		}

		return validatorResult;
	}

}