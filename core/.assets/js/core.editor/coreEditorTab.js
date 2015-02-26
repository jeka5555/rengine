
// Single tab
// ----------
CoreEditorTab = function(args) {

	CoreEditorElement.call(this);

	this.widget = $('<div class="core-editor-element core-editor-tab">/');
	this.update(args);

};

CoreEditorTab.prototype = $.extend({}, CoreEditorElement.prototype, {

	// Update tab's content
	// --------------------
	update : function(args) {
		this.title = args.title;
		CoreEditorElement.prototype.update.call(this, args);
	}
});
