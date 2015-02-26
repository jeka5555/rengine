FlexTabsPanel = function(args) {

	if (args == null) args = {};
	var panel = this;
	Events.init(this);
	this.flexClass = 'Flex.TabsPanel';

	// Settings
	// --------
	this.tabs = {};
	this.activeTab = null;

	// Widget
	// ------
	this.widget = $('<div class="flex-tabsPanel"></div>');
	this.header = $('<div class="flex-tabsPanel-header"></div>').appendTo(this.widget);
	this.content = $('<div class="flex-tabsPanel-content">').appendTo(this.widget);

	// Add widget options
	// ------------------
	if (args.id != null)  { $(this.widget).attr("id", args.id);}
	if (args.class != null)  { $(this.widget).addClass(args.class);}

	// Add tabs
	// --------
	if (args.tabs != null) {

		var firstTabID = null;

		// Add all tabs
		// ------------
		$.each(args.tabs, function(tabIndex, tabData) {
			var tab = panel.addTab(tabData);
			if (firstTabID == null) firstTabID = tab.id;
		});


		// Activate first tab
		// ------------------
		if (firstTabID != null) {
			this.activateTab(firstTabID);
		}

	}
}

FlexTabsPanel.prototype = $.extend({}, Events.prototype, {

	// Activate tab
	// ------------
	activateTab : function(tabID) {

		// Remove active from tabs
		// -----------------------
		$(this.header).children('.flex-tabsPanel-tab-heading').removeClass('active');

		// Don't have tab
		// --------------
		if (this.tabs[tabID] == null) return;

		// Set active tab
		// --------------
		this.activeTab = tabID;

		// Detch old content
		// -----------------
		if (this.tabs[tabID].widget != null) {
			$(this.content).children().detach();
		}

		// Attach new content
		// ------------------
		if (this.tabs[tabID].widget != null) {
			$(this.content).append(this.tabs[tabID].widget);
		}

		// Set active tab header
		// ---------------------
		$(this.header).children('.flex-tabsPanel-tab-heading[data-tab-id=' + tabID + ']').addClass('active');

	},

	// Add tab
	// -------
	addTab : function(args) {

		// Create tab instance
		// -------------------
		var tab = new FlexTab(args);
		var tabsPanel = this;

		// Add heading
		// -----------
		var tabHeading = $('<div class="flex-tabsPanel-tab-heading" data-tab-id=' + tab.id+ '></div>');
		$(this.header).append(tabHeading);

		// Add title
		// ---------
		if (args.title != null) {
			$(tabHeading).html(args.title);
		}

		// Activate tab
		// ------------
		$(tabHeading).click(function() {
			tabsPanel.activateTab(tab.id);
		});

		// Push tab
		// --------
		this.tabs[tab.id] = tab;

		// If this is first one, activate it
		// ---------------------------------
		if (this.activeTab == null) this.activateTab(tab.id);

		// Return tab object
		// -----------------
		return tab;

	}


});

// Tab
// ---
FlexTab = function(args) {

	// Init
	// ----
	Events.init(this);
	var tab = this;

	// Create widget
	// -------------
	this.widget = $('<div class="flex-tab-content"/>');

	// Settings
	// --------
	if (args.id != null) this.id = args.id;
	else {
		this.id = 'tab-' + Math.round(Math.random()*100000);
	}

	// Add content
	// -----------
	if (args.content != null) {
		$(this.widget).append($(args.content));
	}

};

FlexTab.prototype = $.extend({}, Events.init, {});
