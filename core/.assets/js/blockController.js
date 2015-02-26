BlockController = function(args) {

	// This
	// ----
	var controller = this;

	// Init widget
	// -----------
	this.widget = $(args.widget);

	// Add decoration
	// --------------
	$(this.widget).mouseover(function() {$(this).addClass("page-element-active"); });
	$(this.widget).mouseleave(function() { $(this).removeClass("page-element-active");	});

	// Remember
	// --------
	$.extend(this, args);

	// Init
	// ----
	this.initialize(args);

};

BlockController.prototype = $.extend({}, Events.prototype, {

	// Add controller
	// --------------
	initialize : function(args) {

		var controller = this;

		$(this.widget).addClass('page-element-block');
		$(this.widget).attr('title', 'Блок "' + args.id + '"');

		// Drop
		// ----
		$(this.widget).FlexDroppable({
			'contexts' : {

				'widget' : function(data, event, ui) {

					// Move widget to this block
					// -------------------------
					if (data.id != null && data.block != null && controller.id) {

						// Submit action to update
						// -----------------------
						API.action({
							'action' : '/module/widgets/moveWidgetToBlock',
							'data' : {
								'widgetID' : data.id,
								'block' : args.id
							},
							'callback' : function(result) {

								// Goto location
								// -------------
								API.action({ 'action' : '/module/core/reloadPage',
									'data' : { 'location' : Core.location }
								});

							}
						})

					}

				}
			}
		});

	},


});


