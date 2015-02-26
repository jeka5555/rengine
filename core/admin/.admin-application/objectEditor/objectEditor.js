Apps.ObjectEditor = function(args) {

	// This
	// ----
	var objectEditor = this;

	// Create window
	// -------------
	Events.call(this);
	Application.call(this);
	this.window = new Flex.Window({
		'title' : safeAssign(args.title, 'Редактирование объекта'),
		'width' : 900,
		'maximizable' : true,
		'class' : ['object-editor', 'adminTools']
	});

	// Set options
	// -----------
	args.addSaveButton = false;

	// Open editor
	// ----------
	this.editor = {}
	
	if (args == null) args = {};

	// Init core editor element
	// ------------------------
	Events.init(this);
	CoreEditorElement.call(this);

	// Create widget
	// -------------
	this.widget = $('<div class="object-editor">' +
		'<div class="toolbar"></div>' +
		'<div class="content"></div>' +
	'</div>');

	// Subwigets
	// ---------
	this.toolbarWidget = $(this.widget).find(".toolbar");
	this.contentWidget = $(this.widget).find(".content");

    // Add toolbar and save button
    // ---------------------------

	if (args.addSaveButton !== false) {
     this.toolbar = new Flex.WindowToolbar({
       'elements' : [
            {'type' : 'button', 'id' : 'save', 'title' : 'Сохранить', 'click' : function() {
				objectEditor.save();
            }}
        ]
     });
     $(this.toolbarWidget).append(this.toolbar.widget);
	}


	// Load
	// ----
	this.update(args);

	
	$(this.window.widget).append(this.editor.widget);


	// Add save button
	// ---------------
	this.window.windowToolbar.addButton({
		'title' : 'Сохранить',
		'click' : function() {
			objectEditor.save();
		}
	});


}

// Application class
// -----------------
Apps.ObjectEditor.prototype = $.extend({}, Application.prototype, Events.prototype, {

	appID: "objectEditor",
	title: "Редактор объектов",
	
	update : function(args) {

		// Clear editor widget
		// -------------------
		$(this.contentWidget).empty();

		// Settings
		// --------
		this.class = args.class;
		this.id = args.id;
		this.saveURI = safeAssign(args.saveURI, '/module/apps/objectEditor/save');

		// @todo Add object actions

		// Create and append new editor instance
		// -------------------------------------
		this.editor = new CoreEditor(args);
		$(this.contentWidget).append(this.editor.widget);

	},

	// Save object to database
	// -----------------------
	save : function() {

		// This
		// ----
		var objectEditor = this;
		var editorValue = objectEditor.editor.getValue();

		// Save object
		// -----------
		API.action({
			'action' : objectEditor.saveURI,
			'data' : {
				'action' : 'save',
				'class' : objectEditor.class,
				'data' : editorValue
			},

			// Process callback
			// ----------------
			'callback' : function(result) {

				objectEditor.callEvent('complete', editorValue);
				Global.callEvent(['objectUpdate'], editorValue);

				// If success, close a window
				// --------------------------
				if (result != false) {
					objectEditor.close();
				}

				// Else, alert
				// -----------
				else {
					alert('Не удалось сохранить объект, возникла ошибка');
				}
			}
		});

	}
	
});

