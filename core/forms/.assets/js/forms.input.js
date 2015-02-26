// Input collection
// ----------------
UI.FormInputs = {}

// Basic input
// -----------
UI.FormInput = function(args) {

	if (args == null) args = {};

	// Добавляем события
	// ---------------------------
	Events.call(this);

	// Инициализация основных элементов
	// ---------------------------
	this.format = safeAssign(args.format, {});
	this.id = args.id;
	this.widget = $(args.widget);
	this.value = null;

	// Задаем значение
	// -----------------
	if (args.value != null) this.value = args.value;
	else if (this.format.value != null ) this.value = this.format.value;
}

// Общие элементы для различных полей формы
// -------------------------------
UI.FormInput.prototype = $.extend({}, Events.prototype, {

	// Init widget with correct types
	// ------------------------------
	initWidget: function() {

		// Add classes
		// -----------
		if (this.format.type != null) {
			$(this.widget).addClass('input-type-' + safeAssign(this.format.input, this.format.type));
		}

		// Add id class
		// ------------
		if (this.format.id != null) {
			$(this.widget).addClass('input-id-' + this.format.id);
		}

		// Add generic class
		// -----------------
		$(this.widget).addClass('form-input');

	},

	// Возвращаем значение
	// ---------------------------
	getValue : function() {
		return this.value;
	},

	// Get preview value
	// -----------------
	getPreviewValue: function() {
		return null;
	},

	// Set new value
	// -------------
	setValue : function(value) { this.value = value; this.callEvent('change', value); },

	// Decorate description
	// --------------------
	decorateDescription: function(options, wrapper) {

		var format = {};
		if (this.format != null) format = this.format;

		var descriptionWidget = $('<div class="decorated-input-description"></div>').html(format.description);
		$(wrapper).find(".decorated-input-body").append(descriptionWidget);
	},

	// Decorate hint
	// -------------
	decorateHint: function(options, wrapper) {

		var format = {};
		if (this.format != null) format = this.format;
		if (this.format.showHint != true) return;

		var titleWidget = $(wrapper).find('.decorated-input-title');

		$(titleWidget).addClass('has-hint');
        $(titleWidget).attr('title', 'Кликните для получения справки');

		// При клике - показываем
		// ---------------
		$(titleWidget).click(function(e) {

			var hintPopup = $('<div class="core-hint"></div>').html(format.hint);
			$(hintPopup).css({ 'position' : 'absolute', 'top' : e.pageY - 10, 'left' : e.pageX - 10, 'z-index' : 12000 });
			$("body").append(hintPopup);

			// При клике - удаляем
			// -----------
			$(hintPopup).click(function() { $(this).remove(); }).mouseleave(function() { $(this).remove(); })

		});


	},

	// Decorate title
	// --------------
	decorateTitle: function(options, wrapper) {

		var format = {};
		if (this.format != null) format = this.format;

		// If title should be hidden, don't add it
		// ---------------------------------------
		if (this.format.showTitle != true) return;

		var titleWidget = $('<div class="decorated-input-title"></div>').prependTo(wrapper);

		// Add title
		// ---------
		if (format.showTitle == null || format.showTitle != false) {

			var title = safeAssign(format.title, format.id) + ':';

			// Add require mark
			// ----------------
			if (format.validator == 'required') {
				title += ' <span class="input-require-mark">*</span>';
			}


			// Create widget to keep title
			// ---------------------------
			var titleText = $('<span class="title">' + title + '</span>').appendTo(titleWidget);
		}

		// Add field id
		// ------------
		if (format.id != null && format.addID == true ) {
			$(titleWidget).append('<span class="id">(' + format.id + ')</span>');
		}


		// Add units
		// ---------
		if (format.units != null && options.addUnits == true ) {
			$(titleWidget).find(".decorated-input-title").append('<span class="form-input-units"> (' + format.units + ')</span>');
		}


	},

	// Decorate
	// --------
	decorate: function(options) {

	  // Get options
	  // -----------
		if (options == null) options = {};

		// Get input format
		// ----------------
		var format = {};
		if (this.format != null) format = this.format;

		// Make wrapper
		// ------------
		var wrapper = $('<div class="decorated-input"></div>');

		// Add class
		// ---------
		$(wrapper).addClass('input-type-' + safeAssign(format.input, format.type));

		var body = $('<div class="decorated-input-body"></div>').append(this.widget).appendTo(wrapper);

		// Decorate title
		// --------------
		this.decorateTitle(options, wrapper);

		// Decorate descripiton
		// --------------------
		if (format.description != null) {
			this.decorateDescription(options, wrapper);
		}

		// Decorate hint
		// -------------
		if (format.hint != null && options.addHint == true) {
			this.decorateHint(options, wrapper);
		}

		// Return
		// ------
		this.decoratedWidget = wrapper;
		$(this.decoratedWidget).addClass('form-input');
		return wrapper;

	},

	// Сделать перетаскиваемым
	// -----------------------
	makeDraggable: function() {

		if (this.format == null || this.format.draggable != true) return;

		var input = this;
		var dragWidget = $('<div class="form-input-drag"></div>');
		$(this.widget).prepend(dragWidget);

		// Можно бросать другие поля
		// --------------------------
		$(this.widget).FlexDroppable({
			'contexts' : {
				'input' : function(data) {
					if (data.input != null && data.type == input.format.type) input.setValue(data.input.getValue());
				}
			}
		});

		// Принимаем
		// -----------------------
		$(dragWidget).FlexDraggable({
			'useCtrl': true,
			'contexts' : {
				'input' : {'type' : input.format.type, 'input' : this},
			}
		});


	},

	// Очистка ошибок
	// ---------------------------
	clearErrors : function() {
  
    $(this.widget).removeClass('hasErrors');

		// Если уже есть ошибки, удаляем
		// -----------------------                          
		if ($(this.widget).data('errorsWidget') != null) {
			$(this.widget).data('errorsWidget').remove();
		}
	},

	// Добавить ошибки
	// ---------------------------
	errors : function(errors) {   

		// Добавляем класс ошибки
		// -----------------------
    $(this.widget).addClass('hasErrors');

		// Создаем виджет ошибок
		// ------------------------
		var errorsWidget = $('<div class="form-input-errors"></div>');

		// Прикрепляем
		// ------------------------
		$(errorsWidget).insertAfter(this.widget);
		$(this.widget).data('errorsWidget', errorsWidget);

		$(errorsWidget).click(function() {
			$(this).remove();
		});

		// Добавляем блок ошибок
		// -----------------------
		for (var error in errors) {
			$(errorsWidget).append('<div class="form-input-errors-error">' + errors[error] + '</div>');
		}
	},

	// Проверка значения
	// ---------------------------
	validate : function(args) {

		this.clearErrors();

		if (this.format.validator != null) {

			// Забираем значение и прогоняем через валидатор
			// --------------------
			var inputValue = this.getValue();
			var result = UI.Validator.validate(inputValue, this.format.validator, object);

			// Если ответ отрицательный, возврат false
			// --------------------
			if (result != true) {
				this.errors(result);
				return false;
			}
		}

		return true;
	}

});
