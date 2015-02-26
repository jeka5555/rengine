// Управление табами
// ---------------------------------
Flex.Tabs = function(args) {

	var tabs = this;
	this.widget = $(args.widget);
	this.tabs = {};

	// Определяем все табы
	// -----------------------------
	$(this.widget).find("> .flex-tabs-heading > .flex-tabs-heading-button").each(function() {
		var tabID = $(this).data('tabid');
		tabs.tabs[tabID] = {'tab' : $(tabs.widget).find('#'+tabID) , 'heading' : this};
		$(this).click(function() { tabs.setTab(tabID) });
	});

}

Flex.Tabs.prototype = {

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
