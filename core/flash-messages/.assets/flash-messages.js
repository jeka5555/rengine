FlashMessages = {

	// List of messages
	// ----------------
	messages : {},

	// Remove message from bar
	// -----------------------
	remove : function(messageID) {

		// Remove message and widget
		// -------------------------
		$(FlashMessages.messages[messageID].widget).hide("fade");
		delete FlashMessages.messages[messageID];

		// If no any messages, hide panel
		// ------------------------------
		if (Object.keys(FlashMessages.messages).length == 0) {
			$(FlashMessages.widget).hide();
		}

	},

	// Add new message to flash
	// ------------------------
	add : function(message) {

		// Show messages container
		// -----------------------
		$(FlashMessages.widget).show();

		// Create widget
		// -------------
		message.widget = $('<div class="flash-message">' +
			'<span class="icon"></span>' +
			'<span class="text"></span>' +
		'</div>').css({
			'position' : 'relative'
		});

		// Add id
		// -------
		message.id = 'message' + Math.round(Math.random()*100000000000);


		// Add close button
		// ----------------
		var closeButton = $('<div class="close-button"></div>')
			.css({
				'position' : 'absolute',
				'right' : '10px'
			}).appendTo(message.widget);


		// Remove message
		// --------------
		$(closeButton).click(function() {
			FlashMessages.remove(message.id)
		});

		// Add text
		// --------
		if (message.text != null) $(message.widget).find(".text").append(message.text);

		// Add type
		// --------
		if (message.type != null) $(message.widget).attr('data-message-type', message.type);

		// Append to container
		// -------------------
		$(FlashMessages.widget).append(message.widget);

		// Cleanup function
		// ----------------
		if (message.pinned != true) {
			setTimeout(function() { FlashMessages.remove(message.id);}, 5000);
		}

		// Add to collection
		// -----------------
		FlashMessages.messages[message.id] = message;
	}

};

$(function() {

	// Add widget
	// ----------
	FlashMessages.widget = $('<div class="flash-messages"/>')
		.css({
			'position' : 'fixed',
			'z-index' : 10000,
			'top' : 0,
			'left' : 0,
			'right' : 0
		})
		.hide();

	// Append to body
	// --------------
	$(FlashMessages.widget).appendTo("body");

});
