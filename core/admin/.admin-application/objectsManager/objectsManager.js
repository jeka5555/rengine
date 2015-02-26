// Менеджер объектов
// -----------------------
Apps.ObjectsManager = function (args) {

	var objectsManager = this;

	// Инициализация объектов
	// ----------------------
	if (args == null) args = {};

	this.classList = safeAssign(args.classList, null);
	this.currentClass = safeAssign(args.class, null);
	this.classData = {};

	// Selection options
	// -----------------
	this.selectedObjects = {};
	this.selectMode = safeAssign(args.selectMode, false);
	this.multiselect = safeAssign(args.multiselect, false);

	// Lookup options
	// --------------
	this.sort = {};
	this.data = {};
	this.page = 0;
	this.filters = safeAssign(args.filters, {});

	// Select mode
	// -----------
	if (this.selectMode) {
		if (args.classList != null) {
			this.currentClass = args.classList[0];
			this.classList = args.classList;
		}
	}

	// Create application
	// ------------------
	Application.call(this);
	args.title = safeAssign(args.title, 'Объекты');

	// Create window
	// -------------
	this.window = new Flex.Window({
		'title': args.title,
		'width': 900,
		'maximizable' : true,
		'class': ['objects-manager', 'adminTools'],
		'modal': (this.selectMode == true) ? true : false,
		'icon': args.icon
	});

	// Listen object's events
	// ----------------------
	Global.addListener(['objectUpdate', 'objectDelete', 'objectCreate'], function (e) {
		if (e.data.class == objectsManager.currentClass) {
			objectsManager.updateTable();
		}
	});

	// Load initial data
	// -----------------
	this.load();
}


Apps.ObjectsManager.prototype = $.extend({}, Application.prototype, {


	// Загрузка первоначальных данных по объектам
	// --------------------
	load: function () {

		var objectsManager = this;

		API.action({
			'action': '/module/apps/objectsManager/init',
			'data': {
				'classes': this.classList,
				'selectMode': this.selectMode
			},
			'callback': function (data) {

				// Init data
				// ---------
				objectsManager.classes = data.classes;
				objectsManager.init();
			}
		});
	},

	// Инициализация
	// -------------------
	init: function () {

		var objectsManager = this;


		// Создание контейнера
		// -------------------
		this.content = $('<div class="admin-tools-objects-manager-content"></div>');

		// Создание панели инструментов
		// --------------------------------
		this.toolbarPanel = $('<div class="objects-toolbar"></div>').appendTo(this.content);
		this.updateToolbar();

		// Остальные блоки
		// ---------------
		this.contentPanel = $('<div class="objects-content-panel" />');
		$(this.contentPanel).appendTo(this.content);

		// Advanced search panel
		// ---------------------
		this.filtersPanel = new Flex.Tab({'class': 'objects-manager-filters', 'title': 'Расширенный поиск', 'isClosed': false});
		$(this.filtersPanel.widget).appendTo(this.contentPanel).hide();

		// Table panel
		// -----------
		this.objectsTablePanel = new Flex.Tab({'class': 'objects-manager-table', 'title': 'Объекты'});
		$(this.objectsTablePanel.widget).appendTo(this.contentPanel);

		// Toolbar widget
		// --------------
		this.classToolbarWidget = $('<div></div>');
		$(this.classToolbarWidget).appendTo(this.contentPanel);


		// Update
		// ------
		this.update();

		$(this.window.widget).empty().append(this.content);

	},


	// Update whole UI
	// ---------------
	update: function() {
		this.updateTable();
	},

	// Update toolbar
	// --------------
	updateToolbar: function (args) {

		// Init
		// ----
		var manager = this;
		$(this.toolbarPanel).empty();

		// Containers
		// ----------
		var classSelectorContainer = $('<div class="class-selector" />').appendTo(this.toolbarPanel);
		$('<div class="divider" />').appendTo(this.toolbarPanel);
		var searchBoxContainer = $('<div class="search-box-container" />').appendTo(this.toolbarPanel);


		// Class list
		// ----------
		if (this.classList == null || this.classList.length > 1) {

			// Create class selector
			// ---------------------
			var classSelector = new UI.FormInputs.select({
				'type' : 'select',
				'id' : 'class',
				'format' : {
					'values' : this.classes,
					'allowEmpty' : true,
					'emptyText' : '-выберите класс-'
				},
				'value' : this.currentClass
			});

			// Add listener
			// ------------
			classSelector.addListener('change', function(e) {
				manager.setClass(e.data);
			});

			// Add to toolbar
			// --------------
			$(classSelectorContainer).append(classSelector.widget);

		}

		// Search box
		// ----------
		var searchInput = new UI.FormInputs.text({'format': {'id': 'search'}});
		$(searchBoxContainer).append(searchInput.widget);

		// Open advanced search button
		// ---------------------------
		var openSearchButton = new FlexButton({
			'id': 'openAdvancedSearch',
			'toggle' : true,
			'click' : function() {
			$(manager.filtersPanel.widget).toggle();
		}});
		$(searchBoxContainer).append(openSearchButton.widget);


		// Search box event
		// ----------------
		searchInput.addListener('change', function(e) {
			manager.filters = {'@text' : e.data};
			manager.page = 0;
			manager.selection = {};
			manager.updateTable();
		});


		// Add select button
		// -----------------
		if (this.selectMode == true) {
			this.window.windowToolbar.addButton({
				'id': 'select',
				'title': 'Выбрать',
				'click': function () {
					manager.selectionComplete();
				}
			});
		}

	},

	// Update class action buttons
	// ---------------------------
	updateClassActions: function() {

		// If no any class action here, return
		// -----------------------------------
		if (this.classActions == null) return;

		// Create a toolbar
		// ----------------
		var manager = this;
		var actionsPanel =  new Flex.Toolbar({'htmlClass' : 'class-actions-toolbar'});

		// Add buttons
		// -----------
		$.each(this.classActions, function(actionIndex, action) {

			// Click action function
			// ---------------------
			var clickFunction = function() {

				// Add selected objects
				// --------------------
				var selectedObjects = [];

				$.each(manager.selectedObjects, function(index, value) {
					selectedObjects.push(index);
				});

				// Submit action
				// -------------
				API.Objects.classAction({
					'class' : manager.currentClass,
					'action' : action.id,
					'objects' : selectedObjects,
				});
			};

			// Append button
			// -------------
			actionsPanel.appendButton({'id': action.id, 'title' : action.title, 'click' : clickFunction});
		});

		// Add panel
		// ---------
		$(this.objectsTablePanel.content).append(actionsPanel.widget);
	},

	// Обновление таблицы ресурсов
	// ---------------------------
	updateTable: function () {

		var manager = this;

		// If no class selected, no any update actions
		// -------------------------------------------
		if (manager.currentClass == null) {
			manager.updateObjectsList();
			manager.updateFilters();
			return false;
		}

		// Load from server
		// ----------------
		API.action({
			'action': '/module/apps/objectsManager/getObjectsTable',
			'data': {

				'class': manager.currentClass,

				// Display options
				// ---------------
				'page': manager.page,
				'filters': manager.filters,
				'sort': manager.sort
			},
			'callback': function (data) {

				// Guard
				// -----
				if (data == null) return;

				// Set manager's data
				// ------------------
				manager.pagesCount = data.pagesCount;
				manager.tableData = data.tableData;
				manager.tableFormat = data.tableFormat;
				manager.classSearchFormat = data.classSearchFormat;
				manager.objectsCount = data.objectsCount;
				manager.classActions = data.classActions;
				manager.classData = data.classData;

				manager.updateObjectsList();
				manager.updateFilters();

			}
		});
	},

	// Fill table
	// ----------
	updateObjectsList: function (data) {

		var objectsManager = this;

		// Empty panel
		// -----------
		$(this.objectsTablePanel.content).empty();

		// Skip empty sets
		// ---------------
		if (objectsManager.tableData == null) {
			$(this.objectsTablePanel.widget).hide();
			return;
		}
		else {
			$(this.objectsTablePanel.widget).show();
		}

		// Class actions
		// -------------
		objectsManager.updateClassActions();


		// Page navigation
		// ---------------
		if (objectsManager.pagesCount > 0) {
			var paginator = new Flex.Paginator({ count: objectsManager.pagesCount, page: objectsManager.page});

			$(this.objectsTablePanel.content).append(paginator.widget);

			// Listen select event
			// -------------------
			paginator.addListener('select', function (e) {
				objectsManager.page = e.data;
				objectsManager.updateTable();
			});
		}


		// Data table
		// ----------
		this.table = new FlexTable({

			// Basic table settings
			// --------------------
			'select' : true,
			'multiselect' : !(objectsManager.selectMode == true &&  objectsManager.multiselect == false),
			'sort': objectsManager.sort,

			// Columns and data
			// ----------------
			'columns': objectsManager.tableFormat,
			'data': objectsManager.tableData,

			// Row initialization
			// ------------------
			'rowInitialize' : function(args) {

				var row = this;

				// Guards
				// ------
				if (this.data['_id'] == null) {
					console.error('Не задан ID объекта в таблице объектов');
					return;
				}

				// Add cursor
				// ----------
				$(this.widget).css('cursor', 'pointer');

				// Add object's controller
				// -----------------------
				ObjectController.attach({'permanent': true, 'widget': this.widget, 'id': this.data['_id'], 'class': objectsManager.currentClass});

				// Edit with double click
				// ----------------------
				$(this.widget).dblclick(function () {
					API.Objects.action({'action': 'edit', 'class': objectsManager.currentClass, 'id': row.data['_id']});
				});

				// Select event
				// ------------
				this.cells['@select'].addListener('select', function() {
					objectsManager.selectObject(row.data['_id']);
				});

				// Unselect event
				// ------------
				this.cells['@select'].addListener('deselect', function() {
					objectsManager.deselectObject(row.data['_id']);
				});

			}


		});

		// Sort event
		// ----------
		this.table.addListener('sort', function(e) {
			objectsManager.sort = e.data;
			objectsManager.updateTable();
		});

		// Append table
		// ------------
		$(this.objectsTablePanel.content).append(this.table.widget);


		// Set panel title
		// ---------------
		this.objectsTablePanel.setTitle('Объекты класса &laquo;' + this.classData.title + '&raquo;');

	},

	// Remove object from selection
	// ----------------------------
	removeSelectedObjects: function (id) {
		var objectsManager = this;
		var result = false;
		$.each(objectsManager.selectedObjects, function (index, value) {
			if (value.id == id) {
				result = value.id;
				objectsManager.selectedObjects.splice(index, 1);
				return;
			}
		});
		return result;
	},


	// Создание нового объекта
	// -----------------------
	addObject: function () {
		var manager = this;

		// Добавление объекта
		// ------------------
		API.Objects.classAction({
			'action': 'create',
			'class': manager.currentClass,
		});

	},

	// Selection complete
	// ------------------
	selectionComplete: function () {

		var result = [];
		var manager = this;


		// Form correct selection set
		// --------------------------
		$.each(this.selectedObjects, function(index, value) {
			result.push({'class' : manager.currentClass, 'id' : index});
		});

		// Return value
		// ------------
		this.callEvent('select', result);

		// Close application
		// -----------------
		this.window.close();
	},

	// Select object by ID
	// --------------------
	selectObject: function(id) {

		// If not multiselect, drop list
		// -----------------------------
		if (this.selectMode == true && this.multiselect == false) this.selectedObjects = {};

		// Add selected object
		// -------------------
		this.selectedObjects[id] = true;
	},

	// Deselect object
	// ---------------
	deselectObject: function(id) {
		this.selectedObjects[id] = null;
		delete this.selectedObjects[id];
	},

	// Set new class
	// -------------
	setClass: function(classID) {
		this.currentClass = classID;
		this.page = 0;
		this.filters = {};
		this.sort = {};
		this.selection = [];
		this.update();
	},

	// Update filters list
	// -------------------
	updateFilters: function () {

		// Clear panel
		// -----------
		var manager = this;
		$(manager.filtersPanel.content).empty()

		// If no any filters set, return
		// -----------------------------
		if (manager.classSearchFormat == null) {
			$(manager.filtersPanel.widget).hide();
			return;
		}

		// Show panel if hidden
		// --------------------
		else {
			$(manager.filtersPanel.widget).hide();
		}

		// Create search form
		// ------------------
		var form = new UI.Form({
			'format': manager.classSearchFormat,
			'buttons': [ {'id': 'submit', 'title': 'Искать', 'type': 'submit'} ]
		});

		// Add form submit event listener
		// ------------------------------
		form.addListener('preSubmit', function (e) {
			manager.filters = e.data.data;
			manager.updateTable();
		});


		// Refresh
		// -------
		$(manager.filtersPanel.content).append(form.form);

	}

});
