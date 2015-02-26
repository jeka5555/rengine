WidgetController = function(args) {

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
	this.layerID = null;

	// Listen event for object delete
	// ------------------------------
	Global.addListener('objectDelete', function(e) {
		if (e.data.class == 'widget' && e.data.id == controller.id) {
			$(controller.widget).hide({'effect' : 'fade', 'complete' : function() {
				$(this).remove();
			}});
		}
	});

	// Update
	// ------
	Global.addListener('objectUpdate', function(e) {
		if (e.data.class == 'widget' && e.data.id == controller.id) controller.refresh();
	});

	// Init
	// ----
	this.initialize(args);

};

WidgetController.prototype = $.extend({}, Events.prototype, {


	// Refresh widget look
	// -------------------
	refresh : function() {


		// This
		// ----
		var controller = this;

		// Request
		// ------
		API.action({

			// Request
			// -------
			'action' : '/module/widgets/refreshWidget',

			// Collect and submit required data
			// --------------------------------
			'data' : {
				'id' : this.id
			},

			// Callback function
			// -----------------
			'callback' : function(result) {

				// If result is not null
				// --------------------
				if (result != null && result.content != null) {
					$(controller.widget).replaceWith(result.content);
				}

			}
		});

	},

	// Add controller
	// --------------
	initialize : function(args) {

		var controller = this;

		$(this.widget).addClass('page-element-widget');
		$(this.widget).attr('title', 'Виджет ' + '"' + args.title + '" (' + args.widgetTypeTitle + ')');

		// Append menu
		// -----------
		controller.menu = new Flex.ContextMenu({
			'parent' : controller.widget,
			'groups' : [{
				'id' : 'widget' + controller.id,
				'title' : '&laquo;' + controller.widgetTypeTitle + '&raquo;',
				'elements' : [
					{'title' : 'Редактировать', 'group' : 'widget', 'click' : function() { 	API.Objects.action({'action' : 'edit', 'class' : 'widget', 'id' : controller.id}); }},
					{'title' : 'Удалить ', 'group' : 'widget', 'click' : function() { API.Objects.action({'action' : 'delete', 'class' : 'widget', 'id' : controller.id}); }},
				]
			}]
		});

		// Drop
		// ----
		$(this.widget).FlexDroppable({
			'contexts' : {
				'widget' : function(widget) {

					// Move widget to this block
					// -------------------------
					if (widget.block != null && widget.block != args.block) {
						console.log('widget moved');
					}

				}
			}
		});

		// Drag
		// ----
        /*
		$(this.widget).FlexDraggable({
			'contexts' : {
				'widget' : {'type' : args.type, 'block' : args.block, 'id' : args.id }
			}
		});
		*/

	}

});





