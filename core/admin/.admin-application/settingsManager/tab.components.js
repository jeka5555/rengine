// Core manager
// ------------
Component.register({

	// Create component
	// ----------------
	'type' : 'settings-tab',
	'id' : 'components',
	'title' : 'Компоненты',

	// Constructor
	// -----------
	'constructor' : function(args) {

		// This
		// ----
		var editor = this;

		// Create widget
		// -------------
		this.widget = $('<div class="components-list">' +
			'<div class="toolbar">' +
				'<span>Тип компонента: </span><div class="types"></div>' +
				'<span>Компонент: </span><div class="components"></div>' +
			'</div>' +
			'<div class="component-editor"></div>' +
		'</div>');

		// Subwidgets
		// ----------
		this.typesWidget = $(this.widget).find(".types");
		this.componentsWidget = $(this.widget).find(".components");
		this.editorWidget = $(this.widget).find(".component-editor");

		// Update
		// ------
		this.update();
	},

	// Update
	// ------
	'update' : function() {

		var tab = this;

		API.action({
			'action' : '/module/components/getConfigurableComponents',
			'callback' : function(result) {

				tab.components = result;
				tab.type = null;
				tab.id = null;
				tab.updateComponentTypes();
			}
		});

	},

	// Update list of component types
	// ------------------------------
	'updateComponentTypes' : function() {

		// This
		// ----
		var tab = this;

		// Clear widgets
		// -------------
		$(tab.typesWidget).empty();
		$(tab.editorWidget).empty();

		// Add components
		// --------------
		if (this.components != null) {

			// Get component types
			// -------------------
			var types = {};
			$.each(this.components, function(typeID, typeData) {
				types[typeID] = typeData.title;
			});

			// Create selector
			// ---------------
			var typeSelector = new UI.FormInputs.select({
				'format' : {
					'id' : 'type',
					'allowEmpty' : true,
					'values' : types
				}
			});

			// Change event
			// ------------
			typeSelector.addListener('change', function(e) {

				// Set type
				// --------
				tab.type = e.data;
				tab.updateComponentsList();


			});

			// Append widget
			// -------------
			$(tab.typesWidget).append(typeSelector.widget);


		}
	},

	// Update components list
	// ----------------------
	'updateComponentsList' : function() {

		// Tab
		// ---
		var tab = this;

		// Clear widget
		// ------------
		$(tab.componentsWidget).empty();
		$(tab.editorWidget).empty();

		// Build components list
		// --------------------
		var components = {};

		if (tab.components != null && tab.components[tab.type] != null)
		$.each(tab.components[tab.type].components, function(componentIndex, componentData) {
			components[componentIndex] = componentData.title;
		});

		// Create component selector
		// -------------------------
		var componentSelector = new UI.FormInputs.select({
			'format' : { 'values' : components, 'allowEmpty' : true }
		});

		// Change event
		// ------------
		componentSelector.addListener('change', function(e) {
			tab.id = e.data;
			tab.openEditor();
		});


		$(this.componentsWidget).append(componentSelector.widget);


	},


	// Open component editor
	// ---------------------
	'openEditor' : function() {

		// This
		// ----
		var tab = this;

		// Clear editor widget
		// -------------------
		$(this.editorWidget).empty();

		// Request component format
		// ------------------------
		API.action({
			'action' : '/module/components/getComponentEditorFormat',
			'data' : {'type' : tab.type, 'id' : tab.id},
			'callback' : function(result) {

				// Guard
				// -----
				if (result == null) return;

				// Build settings form
				// -------------------
				var editor = new CoreEditor({

					// Data
					// ----
					'properties' : result.properties,
					'elements' : result.structure,
					'data' : result.data,

					// Disable save button
					// -------------------
					'addSaveButton' : false
				});

				// Add save toolbar
				// ----------------
				var toolbar = new Flex.WindowToolbar({
					'elements' : [

						// Save settings
						// -------------
						{'type' : 'button', 'title' : 'Сохранить', 'click' : function() {

							// Read values
							// -----------
							var settings = editor.getValue();

							// Save
							// ----
							if (settings != null) {
								API.action({
									'action' : '/module/components/saveComponentSettings',
									'data' : {
										'type' : tab.type,
										'id' : tab.id,
										'data' : settings
									}
								});
							}
						}},

						// Clear settings
						// --------------
						{'type' : 'button', 'title' : 'Сброс настроек', 'click' : function() {
							API.action({
								'action' : '/module/components/clearComponentSettings',
								'data' : {'type' : tab.type, 'id' : tab.id},
								'callback' : function() {
									tab.openEditor();
								}
							});
						}}
					]
				});

				// Add toolbar
				// -----------
				$(tab.editorWidget).append(toolbar.widget);

				// Add editor
				// -----------
				$(tab.editorWidget).append(editor.widget);

			}
		});


	}
});