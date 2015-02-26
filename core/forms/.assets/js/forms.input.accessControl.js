UI.FormInputs.accessControl = function(args) {

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
			<span title="Добавить" class="miniButton add"><span class="icon"></span> \
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
UI.FormInputs.accessControl.prototype = $.extend({}, UI.FormInput.prototype,{

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
		if (data.length > 0) return data;
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

