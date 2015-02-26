// Управление табами
// ---------------------------------
UI.Tabs = function(args) {

	var tabs = this;
	this.widget = $(args.widget);
	this.tabs = {};

	// Определяем все табы
	// -----------------------------
	$(this.widget).find("> .ui-tabs-heading > .ui-tabs-button").each(function() {
		var tabID = $(this).data('tabid');
		tabs.tabs[tabID] = {'tab' : $(tabs.widget).find('#'+tabID) , 'heading' : this};
		$(this).click(function() { tabs.setTab(tabID) });
	});

}

UI.Tabs.prototype = {

	setTab : function(tabID) {
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
