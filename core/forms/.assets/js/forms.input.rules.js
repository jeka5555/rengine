UI.FormInputs.rules = function(args) {

	// Init
	// ----
	UI.FormInput.call(this, args);
	var input = this;
	
	// Create widget
	// -------------
	this.widget = $('<div> \
		<div class="controls"> \
			<span>Добавить правило</span> \
			<select class="ruleTypes"></select> \
			<span title="Добавить" class="miniButton add"><span class="icon"></span></span> \
		</div> \
		<div class="rules"></div> \
	</div>');

	// All inputs are here
	// -------------------
	this.rules = [];

	// Init widget
	// -----------
	this.init(args);

}
UI.FormInputs.rules.prototype = $.extend({}, UI.FormInput.prototype,{

	// Load rules
	// ----------
	init : function() {

		var input = this;

		// Init rules data
		// ---------------
		if (window['RulesData'] == null) {

			// Submit
			// ------
			API.action({
				'action' : '/module/rules/getRulesInfo',
				'callback' : function(result) {

					// Guard
					// -----
					if (result == null) return;

					// Update rules
					// ------------
					if (result.rules != null) {
						RulesData = result.rules;
					}

					// Update
					// ------
					input.updateWidget();
				}
			})
		}

		// If data alreay exists
		// ---------------------
		else {
			input.updateWidget();
		}
	},

	// Update
	// ------
	updateWidget : function() {

		// This
		// ----
		var input = this;

		// Add rules set
		// -------------
		var rulesSelect = $(this.widget).children(".controls").find("select");

		// Add loaded rules
		// -----------------
		if (RulesData != null) {
			$.each(RulesData, function(ruleIndex, ruleData) {
				var ruleTitle = safeAssign(ruleData.title, ruleIndex);
				var ruleOption = $('<option></option>').attr('value', ruleIndex).html(ruleTitle);
				$(rulesSelect).append(ruleOption)
			});
		}

		// Add content
		// -----------
		this.rulesContent = $(this.widget).children(".rules");

		// Add button
		// ----------
		$(this.widget).children(".controls").find(".miniButton.add").click(function() {
			input.addRule({'type' : $(rulesSelect).val()});
		});


		// Values
		// ------
		if (this.value == null || !(this.value instanceof Array)) return;

		// Add each rule to box
		// --------------------
		$.each(this.value, function(ruleIndex, rule) {
			if (rule.type == null) return;
			input.addRule(rule);
		});
	},


	// Get value
	// ---------
	getValue : function() {
		var data = [];

		// Itterate each rule
		// ------------------
		$.each(this.rules, function(ruleIndex, rule) {
			var value = rule.getValue();
			if (value != null) data.push(value);
		});

		// Return
		// ------
		return data;
	},

	// Set value
	// ---------
	setValue : function(value) {

		this.value = value;
		this.updateWidget();

	},

	// Add rule to list
	// ----------------
	addRule : function(args) {

		var input = this;

		// Block
		// -----
		var ruleWidget = $('<div class="rule-item"> \
			<div class="rule-title">\
				<div class="title"></div>\
				<div class="toolbar"></div>\
			</div> \
			<div class="rule-content"></div> \
		</div>').appendTo(this.rulesContent);

		// Create rule block
		// -----------------
		var ruleBlock = new RuleBlock(args);

		// Add title
		// ---------
		if (ruleBlock.title != null) $(ruleWidget).find('.rule-title .title').append(ruleBlock.title);

		// Remove button
		// -------------
		var contextMenu = new Flex.Toolbar({
			'elements' : [
				{
					'type' : 'button',
					'title' : 'Удалить',
					'click' : function() {
						delete contextMenu;
						$(ruleWidget).remove();
						var position = input.rules.indexOf(ruleBlock);
						if (position != -1) input.rules.splice(position, 1);
					}
				}
			]
		});

		// Append toolbar
		// --------------
		$(contextMenu.widget).appendTo($(ruleWidget).find(".toolbar"));

		// Append to block
		// ---------------
		$(ruleWidget).children(".rule-content").append(ruleBlock.widget);
		this.rules.push(ruleBlock);

	}
});

RuleBlock = function(args) {

	var input = this;
	UI.FormInput.call(this, args);

	// Options
	// -------
	this.type = safeAssign(args.type, 'generic');

	// Get rule data
	//  ------------
	this.ruleData = RulesData[this.type];
	if (this.ruleData == null) return;

	// Get format and title
	// ---------------------
	this.title = this.ruleData.title;
	this.format = this.ruleData.format;
	this.value = args;

	// Create widget
	// -------------
	this.widget = $('<div class="rule-input-rule"></div>');

	// Create form
	// -----------
	if (this.format == null) return;

	// Create form
	// -----------
	this.form = new UI.Form({
		'format' : this.format,
		'object' : this.value
	});

	// Append form to this widget
	// --------------------------
	$(this.widget).append(this.form.form);


}

RuleBlock.prototype = $.extend({}, UI.FormInput.prototype, {

	// Считываем значение
	// -------------------------------------
	getValue : function() {
		return this.form.getValue();
	},

	// Задаем значение
	// ---------------
	setValue : function(value) {
		this.form.setValue(value);
	}
});



UI.FormInputs.ruleOperation = function(args) {

	// Init
	// ----
	UI.FormInput.call(this, args);

	// Selector
	// ---------
	this.selector = new UI.FormInputs.select({
		'value' : args.value,
		'format' : {
			'values' : {
				'$eq' : 'Равно',
				'$ne' : 'Не равно',
				'$true' : 'Всегда да',
				'$false' : 'Всегда нет',
				'$regexp' : 'Соотвествует выражению',
				'$exists' : 'Существует',
				'$null' : 'Не существует',
				'$notnull' : 'Любое существующее значение',
				'$gt' : 'Больше',
				'$gte' : 'Больше или равно',
				'$lt' : 'Меньше',
				'$lte' : 'Меньше или равно',
				'$contain' : 'Содержит текст',
				'$oneOf' : 'Один из'
			}
		}
	});

	// Swap widgets
	// ------------
	this.widget = this.selector.widget;

};


UI.FormInputs.ruleOperation.prototype = $.extend({}, UI.FormInput.prototype, {

	// Get values
	// ----------
	getValue : function() {    
		return this.selector.getValue();
	},

	// Set value
	// ---------
	setValue : function(value) {
		this.selector.setValue(value);
	}
});
