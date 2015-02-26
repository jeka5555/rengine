// Базовый класс формы
// -------------------
Form = function(args) {

	Events.init(this);

	if (args == null) args = {};
	this.args = args;

	// Основные свойства
	// -----------------
	this.format = args.format;
	this.buttons = args.buttons;
	this.formID = args.formID;

	// Присоединение формы
	// -------------------
	if (args.controller != null) {
		this.controllerClass = args.controller;
	}

	// Links
	// -----
	this.method = args.method;
	this.submitURI = args.submitURI;

	// Безопасное извлечение объекта
	// --------------------------
	if (args.object == null) this.object = {};
	else this.object = args.object;

	// Инпуты
	// -----------------------
	this.inputs = {};
	this.buttonInputs = {};

	// Заверешение работы
	// -----------------------
	this.callEvent('formReady');

}

// Базовый прототип всех форм
// --------------------------
Form.prototype = $.extend({}, Events.prototype, {

	// Инициализация
	// -------------
	init : function() {
	},

	// Считываем значения из формы
	// ---------------------------
	getValue : function() {

		var data = {};

		// Забираем данные формы
		// ---------------------------
		$.each(this.inputs, function(index, input) {
			value = input.getValue();
			data[index] = value;
		});

		// Add object's hidden data
		// ------------------------
		if (this.object != null) {
			$.each(this.object, function(id, value) {
				if (data[id] == null) data[id] = value;
			});
		}

		return data;
	},

	// Set form errors
	// ---------------
	errors : function(errors) {	},

	// Success form execution
	// -----------------------
	submit : function() {


		var form = this;
		form.formData = this.getValue();

		// Ready to submit
		// ---------------
		form.callEvent('preSubmit', {'form' : form, 'data' : form.formData });

		// Execute action
		// --------------
		if (form.submitURI != null && form.submitEnabled != false) {

			// Select submit type
			// ------------------
			switch(form.method) {

				// POST
				// ----
				case "POST":
					var localForm = $('<form></form>').attr({'action' : form.actionURI, 'method' : 'POST'});
					$.each(form.formData, function(valIndex, val) {
						if (val != null)
							$(localForm).append('<input type="hidden" name="' + valIndex + '" value="' + val.toString() + '"/>');
					});
					$(localForm).submit();
					form.callEvent('submited');
					break;

	            // GET
				// ---
				case "GET":
					var localForm = $('<form></form>').attr({'action' : form.actionURI, 'method' : 'GET'});
					$.each(form.formData, function(valIndex, val) {
						if (val != null)
							$(localForm).append('<input type="hidden" name="' + valIndex + '" value="' + val.toString() + '"/>');
					});
					$(localForm).submit();
					form.callEvent('submited');
					break;

				// Default
				// -------
				default:

					API.action({
						'action' : form.submitURI,
						'data' : form.formData,
						'callback' : function(result) {

                            form.callEvent('submited', {'result' : result});

							// Clear errors, call event and exit
							// ---------------------------------
							if (result == true) {
								form.clearErrors();
								//form.callEvent('submited', {'result' : result});
								return;
							}

							// Form isn't processed
							// ---------------------
							if (result == null) {
								console.warn('Form isn\'t processed');
								return;
							}

							// Don't set empty errors
							// -----------------------
							form.clearErrors();

							// Forms errors
							// ------------
							if (result.errors != null && result.errors.form != null) {
								form.errors(result.errors.form);
							}

							// Inputs errors
							// -------------
							if (result.errors != null && result.errors.inputs != null) {

								$.each(result.errors.inputs, function(inputID, errors) {
									if (form.inputs[inputID] != null) {
										form.inputs[inputID].errors(errors);
									}
								});

							}


						}
					});
					break;
			}
		}


	}



});


// Конструируем форму поверх HTML кода с заданными параметрами
// ----------------------------------------
UI.Form = function(args) {

	// Init parent
	// -----------
	var form = this;
	Form.call(this, args);

	// Options
	// ---------
	this.decorate = safeAssign(args.decorate, true);
	this.widgetID = args.widgetID;
	this.widget = $(this.widgetID);
	this.class = args.class;

	this.title = args.title;

	// Display options
	// ---------------
	this.addID = safeAssign(args.showInputID, false);
	this.addTitle = safeAssign(args.showInputLabels, true);
	this.addUnits = safeAssign(args.showInputUnits, true);
	this.addHints = safeAssign(args.showInputHints, true);

	// Template
	// --------
	this.template = args.template;
	this.state = args.state;

	// Container of form parts
	// -----------------------
	this.htmlElements = {
		input : {},
		decoratedInput : {},
		buttons : {},
		button : {}
	};

	// Init form elements
	// ------------------
	this.initForm();
	this.initInputs();
	this.initButtons();

	// Hook submit action
	// ------------------
	$(this.form).submit(function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		form.submit();
	});

	// Finish initialization
	// ---------------------
	this.finish();

	// If state is presented, set
	// --------------------------
	if (this.state != null) this.setState(args.state);

	// Call events
	// -----------
	this.callEvent('formReadyUI');
	$(document).trigger('formReadyUI');

}


UI.Form.prototype = $.extend({}, Form.prototype, {

	// Задаем состояние
	// ----------------
	setState : function(state) {
		$.each(this.inputs, function(inputIndex, inputData) {

	  	$(inputData.displayWidget).show();
			if (inputData.format.state == null) return;

			// Для строки
			// ----------
			if (typeof(inputData.format.state) == 'string') {
				if (inputData.format.state != state) $(inputData.displayWidget).hide();
			}

		});
	},


	// Clear form errors
	// -----------------
	clearErrors : function() {
		if (this.errorWidget != null) $(this.errorWidget).remove();
		if (this.inputs != null)
			$.each(this.inputs, function(inputIndex, input) {
					input.clearErrors();
			});
	},

	// Set form errors
	// ---------------
	errors : function(errors) {

		var form = this;

		// Don't set empty errors
		// -----------------------
		this.clearErrors();
		if (errors == null) return;

		// Create error widget
		// ------------------
		this.errorWidget =  $('<div class="core-form-errors"></div>');

		// Add all errors to it
		// --------------------
		$.each(errors, function(errorIndex, errorMessage) {
			$(form.errorWidget).append('<div class="core-form-error">' + errorMessage + '</div>');
		});

		// Append error block
		// -----------------
		if (this.template != null) {
			CoreTemplate.appendToPlaceholder(this.template, 'errors',  this.errorWidget);
		}
		else {
			$(this.form).prepend(this.errorWidget);
		}

	},


	// Инциализация виджета
	// --------------------
	initForm : function() {

		// Create basic form container
		// ---------------------------
		this.form = $('<form class="core-form" method="POST"></form>');

		// Add additional classes
		// ----------------------
		if (this.class != null) $(this.form).addClass(this.class);

		// Add id
		// ------
		if (this.formID != null) {
			$(this.form).attr('data-form-id-' + this.formID);
		}

		// Methods and URI
		// ---------------
		if (this.submitURI != null) $(this.form).attr('action', this.submitURI);
		if (this.method != null) $(this.form).attr('method', this.method);

		// Use template
		// ------------
		if (this.template != null) {
			this.template = $(this.template);
			$(this.form).append(this.template);
		}

	},

	// Append input
	// ------------
	appendInput : function(inputID, inputFormat) {

		// Create input
		// ------------
		var input = this.getInput({ 'format' : inputFormat, 'value' : inputFormat.value, 'id' : inputID });
		this.inputs[inputID] = input;

		// Skip adding widgets for invisibe inputs
		// ---------------------------------------
		if (inputFormat == null || inputFormat.visible == false) return;

		// Add options
		// -----------
		if (this.addTitle == true && inputFormat.showTitle == null) inputFormat.showTitle = true;
		if (this.addHints == true && inputFormat.showHint== null) inputFormat.showHint = true;
		if (this.addID == true && inputFormat.showID == null) inputFormat.showID = true;

		// Get widget
		// ----------
		var inputWidget = input.widget;
		if (this.decorate == true) {
			inputWidget = this.decorateInput(input);
		}

		// Append widget to form
		// ---------------------
		if (this.template != null) {

			// Append to placeholder with ID
			// ------------------------------
			if (inputFormat.templatePlaceholder != null) {
				CoreTemplate.appendToPlaceholder(this.template, inputFormat.templatePlaceholder,  inputWidget);
			}
			// Default placeholder
			// -------------------
			else {
				CoreTemplate.appendToPlaceholder(this.template, inputID, inputWidget);
			}
		}
		// No template used
		// ----------------
		else {
			$(this.form).append(inputWidget);
		}
	},


	// Remove input
	// ------------
	removeInput : function(inputID) {

		// Guard
		// -----
		if (this.inputs[inputID] == null) return;

		// Remove widget
		// -------------
		$(this.inputs[inputID].widget).remove();

		// Delete
		// ------
		delete this.inputs[inputID];

	},

	// Create all form inputs
	// ----------------------
	initInputs : function() {

		var form = this;

		// Add inputs
		// ----------
		if (this.format != null)
		$.each(this.format, function(inputID, inputData) {

			// Pass value
			// ----------
			if (form.object != null && form.object[inputID] != null)  {
				inputData.value = form.object[inputID];
			}
			form.appendInput(inputID, inputData);
		});

	},

	// Generate form buttons
	// ---------------------
	initButtons : function() {

		if (this.buttons == null) return;

		var form = this;

		// Generate button's container
		// ---------------------------
		var buttonsContainer = $('<div class="form-buttons"></div>');
		form.htmlElements.buttons = buttonsContainer;

		// Create button inputs
		// --------------------
		$.each(this.buttons, function(buttonIndex, button) {

			// Create button
			// -------------
			var button = form.getInput({ 'format' : button });
			$(button.widget).removeClass('form-input').addClass('form-button');

			// Append
			// ------
			form.buttonInputs[button.id] = button;
			$(buttonsContainer).append(button.widget);
			form.htmlElements.button[button.id] = button.widget;
		});

		// Add buttons
		//  ----------
		this.formButtons = $('<div class="formButtons"></div>');
		$(this.formButtons).append(this.htmlElements.buttons);

        // Append widget to form
        // ---------------------
        if (this.template != null) {
            CoreTemplate.appendToPlaceholder(this.template, 'buttons', this.formButtons);
        }
        // No template used
        // ----------------
        else {
            $(this.form).append(this.formButtons);
        }




	},

	// Append complete form to container widget
	// ----------------------------------------
	finish : function() {

		var form = this;

		// Add title
		// ---------
		if (this.title != null) {
			this.titleWidget = $('<div class="core-form-title"></div>').html(this.title).appendTo(this.widget);
			$(this.form).prepend(this.titleWidget);
		}


		// Append generated html to widget
		// -------------------------------
		if (this.widgetID != undefined) {
			$(this.widget).append(this.form);
		}

		// If we have some external controller, attach it
		// ----------------------------------------------
        if (this.controllerClass != null) {
            this.controller = new window[this.controllerClass]({ 'form' : this, 'widget' : this.widget});
        }

	},


	// Оформление поля
	// ---------------
	decorateInput : function(input) {
		return input.decorate({ 'addID' : this.addID, 'addUnits' : this.addUnits, 'addHint' : this.addHints });
	},


	// Create form input
	// -----------------
	getInput : function(args) {


		if (args == null) args = {};

		var format = safeAssign(args.format, {});

		var value = null;
		if (args.value != null) value = args.value;
		else if (args.format != null && args.format.value != null) value = args.format.value;

		// Detect input type
		// -----------------
		var inputType = 'generic';

		if (UI.FormInputs[format.input] != null) inputType = format.input;
		else if (UI.FormInputs[format.type] != null) inputType = format.type;
		else { inputType = 'generic'; format.type = 'generic'; }

		// Create an input
		// ---------------
		var input = new UI.FormInputs[inputType]({ 'format' : format, 'value' : value, 'id' : args.id });

		// Append styles and classes
		// -------------------------
		if (format.htmlID != null ) $(input.widget).attr('id', format.htmlID);
		if (format.htmlClasses != null) $.each(format.htmlClasses, function(index, htmlClass) { $(input.widget).addClass(htmlClass); });

		// Add type
		// --------
		var typeID = 'generic';
		if (format.input != null) typeID = format.input;
		else if (format.type != null) typeID = format.type;
		$(input.widget).addClass('input-type-' + typeID);

		return input;
	}



});
