Popup = function(args) {

	Events.call(this);

	if (args.width == null) args.width = 600;

	// Open window to show
	// -------------------
	var popup = this;
	this.window = new Flex.Window(args);
	this.type = safeAssign(args.type, 'message');


	this.data = args.data;
	this.showTitle = safeAssign(args.showTitle, true);

	// Add options
	// -----------
	if (args.popupID != null) $(this.window.widget).addClass(args.popupID).addClass('popup');
	if (args.htmlClass != null) $(this.window.widget).addClass(args.htmlClass);

	// Remove title bar
	// ----------------
	if (this.showTitle == false) {
		$(this.window.widget).dialog('widget').find(".ui-dialog-titlebar").remove();
	}

	// If we need, add button
	// ----------------------
	if (args.buttons != null) {

		// Create button container
		// -----------------------
		var buttons = $('<div class="popup-buttons"></div>')
		var buttonsList = [];

		// Select buttons content
		// ----------------------
		switch (this.type) {

			// Confirm popup
			// -------------
			case "confirm":
				buttonsList.push({'id': 'accept', 'title' : 'Подтверждаю'});
				buttonsList.push({'id': 'reject', 'title' : 'Отмена'})
				break;


			case "accept":
				buttonsList.push({'id': 'ok', 'title' : 'OK'});
				buttonsList.push({'id': 'cancel', 'title' : 'Отмена'})
				break;

			case "yesno":
				buttonsList.push({'id': 'yes', 'title' : 'Да'});
				buttonsList.push({'id': 'no', 'title' : 'Нет'})
				break;

			default:
				buttonsList.push({'id' : 'close', 'title' : 'Закрыть'});
				break;
		}

		// If we have any buttons, add it
		// ------------------------------
		if (buttonsList.length > 0) {
			$.each(buttonsList, function(buttonIndex, button) {

				// Autoclose script
				// ----------------
				if (args.autoClose !== false) button.click = function() {	popup.window.close();	}

				// Create visual representation of the button
				// ------------------------------------------
				var buttonWidget = $('<input type="button" value="' + button.title + '" />');
				$(buttons).append(buttonWidget);

				// Event listener
				// --------------
				if (button.click != null) {
					$(buttonWidget).click(function() {
						button.click();
					});
				}

				// Generic event
				// -------------
				$(buttonWidget).click(function() {
					popup.callEvent('clickButton', {'buttonID' : button.id});
				})
			});
		}

		$(buttons).appendTo(this.window.widget);

	}

	// Attach controller
	// -----------------
	if (args.controller != null) {
		var controllerClass = window[args.controller];
		if (controllerClass != null) {
			this.controller = new window[args.controller]({
				'window' : this.window,
				'popup' : popup
			});
		}
	}

}

Popup.prototype = $.extend(Events.prototype, {});
