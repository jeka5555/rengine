FlexButton = function(args) {

	var button = this;

	Events.init(this);

	// Options
	// -------
	this.icon = args.icon;
	this.title = args.title;
	this.toggle = safeAssign(args.toggle, false);
	this.state = args.state;

	// Widgets
	// -------
	this.widget = $('<div class="flex-button button"/>');
	this.iconWidget = $('<span class="icon"></span>').appendTo(this.widget);
	this.titleWidget = $('<span class="title"></span>').appendTo(this.widget);


	// Enabled state
	// -------------
	this.enabled = safeAssign(args.enabled, true);
	if (this.enabled != true) this.disable();
	else this.enable();

	// Events
	// ------
	if (args.click != null) this.onClick = args.click;
	if (args.onToggle != null) this.onToggle = args.onToggle;

	// Set state
	// ---------
	this.setState(this.state);

	// Click event listener
	// --------------------
	$(this.widget).click(function() {

		if (button.enabled != true) return;

		// Call event
		// ----------
		button.callEvent('click', button.state);

		// Process callback
		// ----------------
		if (button.onClick != null) button.onClick();

		// If toggle button
		// ----------------
		if (button.toggle == true) button.processToggle();

	});

	// Additional classes
	// ------------------
	if (args.class != null) $(button.widget).addClass(args.class);

	// Add icon
	// --------
	if (args.icon != null) this.setIcon(args.icon);

	// ID
	// --
	if (args.id != null) $(this.widget).addClass('id-' + args.id);

	// Add title
	// ---------
	if (args.title != null && args.mode != 'icon') this.setTitle(args.title);
	else $(this.titleWidget).hide();

	if (args.title != null) $(this.widget).attr('title', args.title);

};

// Prototype
// ---------
FlexButton.prototype = $.extend({}, Events.prototype, {


	// Disable
	// -------
	disable : function() {
		this.enabled = false;
		$(this.widget).addClass('disabled');
	},

	// Enable button
	// -------------
	enable : function() {
		this.enabled = true;
		$(this.widget).removeClass('disabled');
	},

	// Toggle
	// ------
	processToggle : function() {

		// Change state
		// ------------
		this.setState(!this.state);

		// Event
		// -----
		if (this.onToggle != null) this.onToggle(this.state);

		// Call
		// ----
		this.callEvent('toggle', this.state);

		// Events
		// ------
		if (this.state) this.callEvent('on');
		else this.callEvent('off');


	},

	// Change state
	// -------------
	setState: function(state) {

		// Set state
		// ---------
		this.state = state;

		// Change class
		// ------------
		if (this.state) $(this.widget).addClass('active');
		else $(this.widget).removeClass('active');
	},

	// Change title
	// ------------
	setTitle: function(title) {

		// Append title
		// ------------
		$(this.titleWidget).empty();
		$(this.titleWidget).append(title);

		// Set title attribute
		// -------------------
		$(this.widget).attr('title', title);
	},

	// Change icon
	// -----------
	setIcon : function(iconURI) {
		$(this.iconWidget).empty().append('<img src="'+iconURI+'"/>');
	}
});