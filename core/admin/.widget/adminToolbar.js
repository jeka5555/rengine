UI.AdminToolbar = function(args)  {

	var toolbar = this;
	var toolbarElements = [];
	this.closed = false;


	// Guard apps list
	// ---------------
	if (args.apps == null || args.apps.length == 0) return;

	// Add application buttons
	// -----------------------
	$.each(args.apps, function(appIndex, appInfo) {

		// Если есть комманда
		// ------------------
		if (appInfo.command != null) {
			var clickFunction	= function() { eval(appInfo.command); }
		}

		else {
			var clickFunction =  function() { Apps.start(appInfo.id, args, appInfo); }
		}

		toolbarElements.push({
			'type' : 'button',
			'mode' : 'icon',
			'class' : appInfo.id,
			'icon' : appInfo.icon,
			'title' : appInfo.title,
			'click' : clickFunction
		});

	});

	// Toggle panel button
	// -------------------
	toolbarElements.push({
		'id' : 'rengine',
		'type' : 'button',
		'class' : 'minimize',
		'mode' : 'icon',
		'icon' : '/core/.assets/img/icons/rengine.png',
		'click' : function() {
			toolbar.toggle();
		}
	});

	// Add panel
	// ---------
	Flex.Toolbar.call(this, {
		'parent' : "body",
		'mode' : 'stick',
		'buttonMode' : 'icon',
		'elements' : toolbarElements
	});

	// Add classes
	// -----------
	$(this.widget).addClass('admin-toolbar').css({'position' : 'fixed', 'overflow' : 'hidden', 'z-index' : 150});

	// Close
	// -----
	this.toggle();
}


UI.AdminToolbar.prototype = $.extend({}, Flex.Toolbar.prototype, {

	toggle : function() {

		var toolbar = this;

		if (this.closed) {

			this.closed = false;
			$(this.widget).css("opacity", 1);

			toolbar.elements['rengine'].setIcon('/core/.assets/img/icons/rengine-closed.png');

			$(this.widget).children().each(function() {
				if (! $(this).hasClass('minimize')) {
					$(this).show();
				}
			});
		}
		else {
			this.closed = true;

			toolbar.elements['rengine'].setIcon('/core/.assets/img/icons/rengine.png');

			$(this.widget).css("opacity", 0.5);
			$(this.widget).children().each(function() {
				if (! $(this).hasClass('minimize'))	$(this).hide();
			});
		}
	}
});
