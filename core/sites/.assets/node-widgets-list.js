SiteNodeWidgetsList = function(args) {

	var widgetsList = this;

	CoreEditorElement.call(this, args);
	
	this.args = args;
	this.property = args.property;
	this.editor = args.editor;    
	
	// Widgets
	// -------
	this.widget = $('<div class="nodes-widgets-list"></div>');
	this.update(args);

	Global.addListener(['objectUpdate', 'objectDelete'], function(e) {

		if (e.data.class != 'widget') return;

		// Check if update is required
		// ---------------------------
		var requireUpdate = false;
		if (widgetsList.widgets != null) {
			$.each(widgetsList.widgets, function(widgetID, widgetData) {
				if (e.data.id == widgetData.id) requireUpdate = true;
			});
		}

		// Update widget
		// -------------
		if (requireUpdate) widgetsList.updateWidget();

	});
}

SiteNodeWidgetsList.prototype = $.extend({}, CoreEditorElement.prototype, Events.prototype, {

	// Update data
	// -----------
	update : function(args) {

		// This
		// ----
		widgetsList = this;

		// Clear widgets
		// --------------
		var nodeID = args.editor.properties['_id'].value;

		// Get parent value
		// ----------------
		var parent = null;
		if (args.editor.properties['parent'] != null) args.editor.properties['parent'].value;

		// Set value
		// ---------
		if (args.editor.data.widgets != null)
			this.value = args.editor.data.widgets;
		else
			this.value = [];	

		// Request list of widgets
		// -----------------------
		API.action({
			'action' : '/module/apps/nodesManager/getNodeWidgets',
			'data' : {
				'nodeID' : nodeID,
				'parent' : parent
			},
			'callback' : function(result) {
				if (result != null) {
					widgetsList.widgets = result;
					widgetsList.updateWidget();
				} else {
					widgetsList.widgets = [];
				}
			}
		})
	},


	// Update widget
	// -------------
	updateWidget : function() {

		$(this.widget).empty();
		var widgetsList = this;

		// Add widget button
		// -----------------
		this.toolbar = new Flex.Toolbar({
			'elements' : [

				// Add widget button
				// -----------------
				{'type' : 'button', 'title' : 'Добавить виджет', 'click' : function() {
					widgetsList.createWidget();
				}},

				// Clear button
				// ------------
				{'type' : 'button', 'title' : 'Очистить', 'click' : function() {

				}}
			]
		});
		$(this.widget).append(this.toolbar.widget);


		// Table format
		// ------------
		var tableFormat = [
			{id : 'type', title : 'Тип', type : 'text'},
			{id : 'role', title : 'Роль', type : 'text'},
			{id : 'title', title : 'Название', type : 'text', sortable : true},
			{id : 'block', title : 'Блок', type : 'text', sortable : true},
			{id : 'order', title : 'Порядок вывода', sortable : true},
			{id : 'buttons', type : 'text', 'css' : {'white-space' : 'nowrap', 'text-align' : 'center'} }
		];


		// Build table data
		// ----------------
		var tableData = [];

		// Create nodes table
		// ---------------------
		this.table = new FlexTable({
			'columns' : tableFormat,
			'sort' : widgetsList.sort,
			'data' : widgetsList.widgets,
			'rowInitialize' : function() {

				// This
				// ----
				var row = this;

				$(this.widget).css({'cursor' : 'pointer'});

				// Edit on click
				// -------------
				$(this.widget).dblclick(function () {
					API.Objects.action({'action': 'edit', 'class': 'widget', 'id': row.data['id']});
				});

				// Attach controller
				// -----------------
				ObjectController.attach({
					'permanent' : true,
					'widget' : this.widget,
					'id' : this.data['id'],
					'class' : 'widget'
				});

				// Delete button
				// -------------
				var editButton = new FlexButton({
					'title' : 'Редактировать',
					'id' : 'edit',
					'mode' : 'icon',
					'click' : function() {
						API.Objects.action({'action': 'edit', 'class': 'widget', 'id': row.data['id']});
					}
				});
				row.cells['buttons'].widget.append(editButton.widget);

				// Edit button
				// -----------
				var deleteButton = new FlexButton({
					'title' : 'Удалить виджет из списка',
					'id' : 'delete',
					'mode' : 'icon',
					'click' : function() {

						// Remove from value
						// -----------------
						$(widgetsList.value).each(function(widgetIndex, widgetData) {
							if (widgetData.id == row.data['id']) {
								widgetsList.value.splice(widgetIndex,1);
							}
						});

						// Remove from widgets
						// -------------------
						$(widgetsList.widgets).each(function(widgetIndex, widgetData) {
							if (widgetData.id == row.data['id']) {
								widgetsList.widgets.splice(widgetIndex,1);
							}
						});

						// Update widget
						// -------------
						widgetsList.updateWidget();

					}
				});
				row.cells['buttons'].widget.append(deleteButton.widget);

			}
		});

		// Add table
		// ---------
		$(this.table.widget).appendTo(this.widget);


	},


	// Create new widget
	// -----------------
	createWidget : function() {
	
		var widgetsList = this;
		
		API.Objects.classAction({'action': 'create', 'class': 'widget', 'callback' : function(result) {

			if (result == null) return;
			var editor = window[result];

			if (editor == null) {
				return;
			}
			// Bind editor
			// -----------
			editor.addListener('complete', function(e) {
			
				var nodeID = widgetsList.editor.properties['_id'].value; 
				widgetsList.value.push({
					'id' : e.data['_id'],
				});
				
				API.Objects.action({
					'action' : 'update',
					'class' : 'node',
					'id' : nodeID, 
					'data' : {'widgets' : widgetsList.value},
					
					// Process callback
					// ----------------
					'callback' : function(result) {
						widgetsList.update(widgetsList.args);
					}
				});

			});
			
		}});

	},


	// Set value
	// ---------
	setValue : function(value) {
		this.value = value;
	},

	// Get value
	// ---------
	getValue : function() {
		return this.value;
	}
});
