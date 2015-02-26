// Управление табами
// ---------------------------------
CoreTabs = function(args) {

	var tabs = this;
	this.widget = $(args.widget);
	this.tabs = {};

	// Определяем все табы
	// -----------------------------
	$(this.widget).find(".core-tab-heading > .core-tab-button").each(function() {
		var tabID = $(this).attr('data-tab-id');
		tabs.tabs[tabID] = {'tab' : $(tabs.widget).find('.core-tab-pane[data-tab-id=' + tabID + ']') , 'heading' : this};
		$(this).click(function() { tabs.setTab(tabID) });
	});

	// Делаем таб активным
	// -------------------
	$(this.widget).find(".core-tab-heading > .core-tab-button").each(function() {
		var tabID = $(this).attr('data-tab-id');
		if ($(this).hasClass('active')) tabs.setTab(tabID);
	});

}

CoreTabs.prototype = {

	setTab : function(tabID) {

		var tabs = this;

		$(tabs.widget).find(".core-tab-heading > .core-tab-button").removeClass('active');
		$(tabs.widget).find(".core-tab-heading > .core-tab-button[data-tab-id=" + tabID + "]").addClass('active');

		$.each(this.tabs, function(tabIndex, tabData) {
			if (tabIndex != tabID) {
				$(tabData.tab).hide();	
				$(tabData.heading).removeClass('active');
			}
			else {
				$(tabData.tab).show();
				$(tabData.heading).addClass('active');
			}
		});
	}

}


$(document).bind('init', function() {
	$(".core-tabs").each(function(tabsIndex, tabs) {	
		var tab = new CoreTabs({'widget' : tabs});
	});
});
