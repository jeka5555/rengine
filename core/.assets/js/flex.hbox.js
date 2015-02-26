FlexHBox = function(args) {

	var flexbox = this;

	// Create widget
	// -------------
	this.widget = $('<div class="flex-hbox"></div>');

	// Children
	// --------
	this.elements = [];

	// Add content
	// -----------
	if (args.content != null) {

		$.each(args.content, function(elementIndex, element) {

			// Add element to container
			// ------------------------
			$(flexbox.widget).append(element.widget);

			// Add to internal elements
			// ------------------------
			flexbox.elements[elementIndex] = element;
		});
	}

}