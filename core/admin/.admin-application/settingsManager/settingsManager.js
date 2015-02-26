Apps.SettingsManager = function(args) {

	// Init
	// ----
	var manager = this;
	if (args == undefined) args = {};

    // Set title
    // ---------
	args.title = safeAssign(args.title, 'Управление настройками');

	// Create window and parts
	// -----------------------
	Application.call(this);
	this.window = new Flex.Window({ 'title' : args.title, 'class' : ['settings-manager','adminTools'], 'maximizable' : true, 'width' : 800, 'minHeight' : 200, 'icon' : args.icon});
	this.widget = $('<div class="container"></div>').appendTo(this.window.widget);

	// Init
	// ----
	manager.init();
}


Apps.SettingsManager.prototype = $.extend({}, Application.prototype, {

	appID : 'settingsManager',

	// Init application
	// ----------------
	init : function() {

		var manager = this;

		var tabs = Components.getByType('settings-tab');
		if (tabs != null) {

			// Create tabs
			// -----------
			var tabsPanel = new FlexTabsPanel();
			$(manager.widget).append(tabsPanel.widget);

			// Add item tabs
			// -------------
			$.each(tabs, function (tabIndex, tabComponent) {

				// Create tab item
				// ---------------
				var tabContent = tabComponent.getInstance();

				tabsPanel.addTab({'title' : tabComponent.title, 'content' : tabContent.widget});


			});
		}
	}

});


