// Панель инструментов для окна
// ----------------------------
Flex.WindowToolbar = function(args) {

	var toolbar = this;
	Events.init(this);

	// Options
	// -------
	if (args == null) args = {};
	this.elements = safeAssign(args.elements, []);

	// Create widget
	// -------------
	this.widget = $('<div class="flex-window-toolbar"></div>');
	$(this.widget).hide();


	// Add elements
	// ------------
	$.each(this.elements, function(elementIndex, element) {
		toolbar.addButton(element);
	});

}

Flex.WindowToolbar.prototype = $.extend({}, Events.prototype, {

	clear: function() {
		this.elements = [];
		$(this.widget).empty();
	},

	// Add buttons
	// ------------
	addButton : function(args) {

		$(this.widget).show();

		// Add code
		// --------
		this.elements.push(args);

		// Create button
		// -------------
		var button = new FlexButton(args);
		$(this.widget).append(button.widget);

		return button;

	}
});
