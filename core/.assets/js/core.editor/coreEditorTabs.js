
// Core editor tabs
// ----------------
CoreEditorTabs = function(args) {

	// Init
	// ----
	CoreEditorElement.call(this);

	// Create
	// ------
	this.widget = $('<div/>');
	this.update(args);

};

// Update tab structure
// --------------------
CoreEditorTabs.prototype = $.extend({}, CoreEditorElement.prototype, {

	updateWidget : function() {

		// This
		// ----
		var editorTabs = this;

		// Clear widget
		// ------------
		$(this.widget).empty();

		// Create new tabls  panel
		// -----------------------
		this.tabsPanel = new FlexTabsPanel();

		if (this.nodes != null)
		$.each(this.nodes, function(nodeIndex, node) {

			// Update element's widget
			// ----------------------
			node.updateWidget();

			// Append tab
			// ----------
			editorTabs.tabsPanel.addTab({
				'title' : node.title,
				'content' : node.widget
			})
		});

		// Add tabs to this widget
		// -----------------------
		$(this.tabsPanel.widget).appendTo(this.widget);

	}
});
