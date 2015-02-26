Component.register({

	// Create component
	// ----------------
	'type' : 'component',
	'id' : 'content-block',
	'title' : 'Блок для вывода редактируемого контента',

	// Constructor
	// -----------
	'constructor' : function(args) {

		if (args == null) args = {};
		Events.call(this);

		var block = this;

		// Settings
		// --------
		this.mode = safeAssign(args.mode, 'edit');
		this.data = args.data;

		// Decoration purpose data
		// -----------------------
		this.css = safeAssign(args.css, []);
		this.htmlAttributes = safeAssign(args.htmlAttributes, {});
		this.htmlClasses = safeAssign(args.htmlClasses, []);
		this.htmlID = safeAssign(args.htmlID, null);

		// Create widget
		// -------------
		this.widget = $('<div class="content-editor-block">' +
			'<div class="table">' +
				'<div class="drag-handle"></div>' +
				'<div class="content"></div>' +
				'<div class="toolbar"></div>' +
			'</div>' +
		'</div>').attr('tabindex', -1);


		// Focusing
		// --------
		$(this.widget).focusin(function() { $(block.widget).addClass('focused'); });
		$(this.widget).focusout(function() { $(block.widget).removeClass('focused'); });

		// Attach content block
		// --------------------
		$(this.widget).data('contentBlock', this);

		// Widgets
		// -------
		this.toolbarWidget = $(this.widget).find(".toolbar");
		this.contentWidget = $(this.widget).find(".content");

		// Create toolbar
		// --------------
		this.updateToolbar();
		this.updateWidget();
	},


	// Update toolbar
	// --------------
	'updateToolbar' : function() {

		// This
		// ----
		var block = this;

		// Delete button
		// -------------
		var deleteButton = new FlexButton({
			'title' : 'Удалить',
			'id' : 'delete',
			'mode' : 'icon',
			'click' : function() {
				block.callEvent('delete');
			}
		});
		$(this.toolbarWidget).append(deleteButton.widget);


		// Delete button
		// -------------
		var settingsButton = new FlexButton({
			'title' : 'Настройки',
			'id' : 'settings',
			'mode' : 'icon',
			'click' : function() {
				block.openBlockSettings();
			}
		});
		$(this.toolbarWidget).append(settingsButton.widget);


	},

	// Update widget
	// -------------
	'updateWidget' : function() {

		$(this.contentWidget).empty();

		switch (this.mode) {

			// Preview
			// -------
			case "edit":
				this.renderEdit();
				break;

			// Default
			// -------
			default:
				this.value = this.getValue();
				this.renderPreview();
				break;
		}

	},

	// Render
	// ------
	'renderEdit' : function() {
		console.log('method is not implemented');
	},

	// Set mode
	// --------
	'setMode' : function(mode) {

		// Set mode if it's not active
		// ---------------------------
		if (this.mode == mode) return;
		this.mode = mode;

		// Update widget
		// -------------
		this.updateWidget();
	},

	// Get value
	// ---------
	'getValue' : function() {

		// Return null for nulls
		// ---------------------
		if (this.data == null) return null;

		// Return default content block format
		// -----------------------------------
		var result = {'data' : this.data, 'type' : this.type };

		// Clean up unused values
		// ----------------------
		if (this.htmlID != null) result['htmlID'] = this.htmlID;
		if (this.htmlClasses != null && this.htmlClasses.length > 0) result['htmlClasses'] = this.htmlClasses;
		if (this.htmlAttributes != null) result['htmlAttributes'] = this.htmlAttributes;
		if (this.css != null && this.css.length > 0) result['css'] = this.css;

		// Return
		// ------
		return result;

	},

	// Edit settings
	// -------------
	'openBlockSettings' : function() {

		// This
		// ----
		var block = this;

		// Create settings window
		// ----------------------
		var win = new Flex.Window({
			'title' : 'Измненение настроек',
			'width' : 600,
			'class' : ['adminTools']
		});

		// Create form
		// -----------
		var form = new UI.Form({
			'format' : {
				'tag' : {'type' : 'select', 'title' : 'Тэг отображения', 'allowEmpty' : true, 'values' : {'div' : 'DIV', 'span' : 'SPAN'}},
				'htmlID' : {'type' : 'text', 'title' : 'ID-элемента'},
				'htmlClasses' : {'type' : 'list', 'title' : 'HTML-классы', 'format' : {'type' : 'text'}},
				'htmlAttributes' : {'type' : 'record', 'title' : 'Аттрибуты HTML'}
			},

			// Pass data
			// ---------
			'object' : {
				'htmlTag' : this.htmlTag,
				'htmlID' : this.htmlID,
				'htmlClasses' : this.htmlClasses,
				'htmlAttributes' : this.htmlAttributes
			}
		});

		// Save button
		// -----------
		win.windowToolbar.addButton({
			'title' : 'Сохранить',
			'click' : function() {

				// Get form value
				// --------------
				var formValue = form.getValue();

				// Assign
				// ------
				block.htmlID = formValue.htmlID;
				block.htmlAttributes = formValue.htmlAttributes;
				block.htmlClasses = formValue.htmlClasses;
				block.css = formValue.css;

				// Close window
				// ------------
				win.close();

			}
		});

		$(win.widget).append(form.form);

	},

	// Render block preview
	// --------------------
	'renderPreview' : function() {

		// This
		// ----
		var block = this;

		// Request render
		// --------------
		API.action({
			'action' : '/module/content/renderBlock',
			'data' : {
				'type' : this.id,
				'data' : this.getValue()
			},

			// Result
			// ------
			'callback' : function(result)  {

				$(block.contentWidget).empty();

				if (result != null) {
					$(block.contentWidget).append(result);
				}
			}
		})
	}


});

