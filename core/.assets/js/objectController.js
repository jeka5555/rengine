ObjectController = function(args) {

	var objectController = this;

	// Options
	// -------
	this.id = args.id;
	this.class = args.class;
	this.widget = args.widget;
	this.useMenu = safeAssign(args.useMenu, true);
	this.useDragAndDrop = safeAssign(args.useDragAndDrom, true);
	this.useWrapper = safeAssign(args.useWrapper, false);
	this.permanent = args.permanent;
	this.actions = args.actions;


	// Create an binding
	// -----------------
	this.bindingObject = $(this.widget).data('objectBinding');

	// If this widget already binded to this object, skip
	// ---------------------------------------------------
	if (this.bindingObject == null) {
		this.bindingObject = {};
		$(this.widget).data('objectBinding', this.bindingObject);
	}


	if (this.bindingObject['object-' + this.class + '-' + this.id] != null) return;
	this.bindingObject['object-' + this.class + '-' + this.id] = this;


	// Listen event for object delete
	// ------------------------------
	Global.addListener('objectDelete', function(e) {
		if (e.data.class == objectController.class && e.data.id == objectController.id)
			$(objectController.widget).hide({'effect' : 'fade', 'complete' : function() {
				$(this).remove();
			}});
	});

	// Init UI
	// --------
	this.initialize();

};


ObjectController.attach = function (args) {
	var objectController = new ObjectController(args);
	return objectController;
}


ObjectController.prototype = $.extend({}, Events.prototype, {

	// Init UI
	// -------
	initialize : function() {

		// This
		// ----
		var controller = this;

		// Decoration events
		// -----------------
		$(this.widget).mouseover(function() { $(this).addClass("page-element-active"); });
		$(this.widget).mouseleave(function() { $(this).removeClass("page-element-active"); });

		// Append menu
		// -----------
		if (this.useMenu) {

			// Создаем список элементов
			// ------------------------
			var menuElements = [];

			// Actions from arguments
			// ----------------------
			if (this.actions != null) {

				$.each(this.actions, function(actionIndex, action) {
					menuElements.push({
						'group' : action.group,
						'text' : action.title,
						'click' : action.click
					});
				});

			}

			// Actions from core
			// -----------------
			API.Objects.action({
				'action' : 'getInfo',
				'class' : controller.class,
				'id' : controller.id,
				'callback' : function(result) {

					// Skip empty list
					// ----------------
					if (result == null) return;

					// Get class name
					// -------------
					if (result.className != null) controller.className = result.className;

					// Get object identity
					// --------------------
					if (result.identity != null) $(controller.widget).attr('title', result.identity);

					// Create menu group
					// -----------------
					var menuGroup = 'object-' + controller.class + '-' + controller.id;

					// List of actions
					// ---------------
					if (result.actions != null && controller.widget != null) {

						$.each(result.actions, function(actionIndex, action) {
							menuElements.push({
								'group' : menuGroup,
								'title' : action.title,
								'click' : function() {

									// Функция
									// -------
		  						    var actionFunc = function() {
										API.Objects.action ({
											'action' : action.action,
											'class' : controller.class,
											'id' : controller.id
										});
									};

									// С вопросом или без?
									// -------------------
									if (action.requireConfirmation == true) {
										if (confirm(safeAssign(action.confirmationMessage,'Вы уверены?'))) actionFunc();
									}
									else {
										actionFunc();
									}

								}
							});
						});

					}

					// If menu already exists, append it
					// ----------------------------------
					if (menuElements.length > 0) {

						// Append menu
						// -----------
						controller.menu = new Flex.ContextMenu({
							'parent' : controller.widget,
							'groups' : [{
								'id' : menuGroup,
								'title' : safeAssign(controller.className, 'Объект ' + controller.class),
								'elements' : menuElements
							}]
						});
					}

				}
			});

		}

		// Add object wrapper
		// ------------------
		if (this.useWrapper) { $(this.widget).addClass('page-element-object'); }

		// Listen drag and drop
		// ---------------------
		if (this.useDragAndDrop) {

			// Hook drop events
			// --------------------
			$(this.widget).FlexDroppable({
				'drop' : function(context) {
					if (context != null && context.contexts != null) controller.drop(context.contexts);
				}
			});

			// Append drag context
			// -------------------
			$(this.widget).FlexDraggable({
				'contexts' : { 'object' : {'id' : controller.id, 'class' : controller.class } }
			});
		}

	},

	// Drop event
	// ----------
	drop : function(context) {

		// This
		// ----
		var controller = this;

		// Вызов функции
		// -------------
		API.Objects.action ({
			'action' : 'drop',
			'class' : controller.class,
			'id' : controller.id,
			'data' : context
		});

	}

});
