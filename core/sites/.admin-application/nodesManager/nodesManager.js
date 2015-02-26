Apps.NodesManager = function(args, appOptions) {

	// Init
	// ----
	var nodesManager = this;
	if (args == null) var args = {};
	if (appOptions == null) var appOptions = {};

	// Options
	// -------
	this.tree = [];

	this.nodeID = args.parent;  // Parent node for tree mode
	this.target = null;

	// Selection options
	// -----------------
	this.selectMode = safeAssign(args.selectMode, false);
	this.multiselect = safeAssign(args.multiselect, true);
	if (args.nodeType != null) this.type = args.nodeType;
	if (args.mode != null) this.mode = args.mode;
	this.selectedObjects = {};
 
	// Create an application window
	// ---------------------------
	Application.call(this);
	this.window = new Flex.Window({'title' : appOptions.title, 'width': 1024, 'height': 600, 'maximizable' : true, 'class' : ['nodes-manager','adminTools'], 'icon' : appOptions.icon});

	// In select mode, append complete button
	// --------------------------------------
	if (this.selectMode == true) {
		this.window.windowToolbar.addButton({
			'id' : 'select',
			'title' : 'Выбрать',
			'click' : function() {
				nodesManager.selectionComplete();
			}
		});
	}

	// Create nodes button
	// ----------------------
	this.window.windowToolbar.addButton({
		'id' : 'create-node',
		'title' : 'Создать',
		'click' : function() {
			API.Objects.classAction({
				'action' : 'create',
				'class' : 'node',
				'data' : {
					'parent' : nodesManager.nodeID ,
					'type' : nodesManager.type
				}
			});
		}
	});

	// Mode
	// ----
	this.window.windowToolbar.addButton({
		'id' : 'toggle-mode',
		'toggle' : true,
		'mode' : 'icon',
		'state' : (nodesManager.mode == 'tree'),
		'title' : 'Как список',
		'click' : function(e) {
			if (nodesManager.viewer != null) {

				if (nodesManager.viewer.mode == 'list') {
					nodesManager.viewer.setMode('tree');
				}
				else {
					nodesManager.viewer.setMode('list');
				}
			}
		}
	});


	// Load data
	// ---------
	this.update();

};

Apps.NodesManager.prototype = $.extend({}, Events.prototype, Flex.Window.prototype, {

	// Update
	// ------
	update : function() {

		var manager = this;

		API.action({
			'action' : '/module/apps/nodesManager/init',

			// Submit all data
			// ---------------
			'data' : {
				'nodeID' : manager.nodeID
			},

			// Init data
			// ---------
			'callback' : function(result) {

				if (result == null) return;

				// Set data
				// --------
				manager.tree = result.tree;
				manager.systemNodes = result.systemNodes;
				manager.viewerComponent = safeAssign(result.viewerComponent, 'default');

				// UI
				// ---
				manager.buildUI();
				manager.updateTree();
				manager.updateViewer();
			}
		});

	},

	// Init wiew
	// ---------
	buildUI : function() {

		var manager = this;
		$(this.window.widget).empty();

		$(this.window.widget).html(
			'<div class="nodes-manager-filters"></div>' +
			'<div class="nodes-manager-content">' +
			'</div>'
		);

		// Create container
		// ----------------
		this.content = $(this.window.widget).find('.nodes-manager-content');
	    this.filtersPanel = $(this.window.widget).find(".nodes-manager-filters");

		// Tree panel
		// ----------
		this.sidePanel = new Flex.Panel({'class' : 'nodes-manager-tree'});
		$(this.sidePanel.widget).appendTo(this.content);

		// Control panel
		// -------------
		this.contentPanel = new Flex.Panel({'class' : 'nodes-manager-panel'});
		$(this.contentPanel.widget).appendTo(this.content);

		// Nodes table
		// ---------------
		this.viewerPanel = new Flex.Panel({'class' : 'nodes-manager-table'});
		$(this.viewerPanel.widget).appendTo(this.contentPanel.widget);


	},

	// Update navigation tree
	// ----------------------
	updateTree : function(args) {

		// Clear panel
		// -----------
		var manager = this;
		$(this.sidePanel.widget).empty();

		// Create tree panel
		// -----------------
		this.treePanel  = new Flex.Panel({'title' : 'Навигация'});
		$(this.sidePanel.widget).append(this.treePanel.widget);

		// Create system nodes panel
		// -------------------------
		this.systemNodesPanel = new Flex.Panel({'title' : 'Системные узлы'});
		$(this.sidePanel.widget).append(this.systemNodesPanel.widget);

		// Make tree navigation
		// --------------------
		this.nodesTree = new Flex.Tree({

			'children' : manager.tree,

			// Each path element
			// ------------------
			'extendPath' : {
				'click' : function() {
					manager.openNode(this.data['id']);
				}
			},

			// Расширение нод
			// --------------
			'extendNode' : {
				'isClosed' : true,
				'initialize' : function() {

					// Get node id
					// -----------
					var nodeID = this.data.id;

					// Active
					// ------
					if (this.data.active == true) {
						$(this.widget).addClass('active');
					}


					// Click event
					//  ----------
					$(this.widget).children('.flex-tree-node-heading').each(function() {

						// Add click event
						// ---------------
						$(this).click(function() { manager.openNode(nodeID); });

						// Add object controller
						// ---------------------
						ObjectController.attach({'permanent' : true, 'widget' : this, 'id' : nodeID, 'class' : 'node'});

					});

				}

			}
		});

		$(this.nodesTree.widget).appendTo(this.treePanel.widget);



		// Append system nodes
		// -------------------
		if (this.systemNodes != null) {
			$.each(this.systemNodes, function(nodeIndex, nodeData) {


				// Create widget
				//  ------------
				var nodeWidget = $('<div class="nodes-manager-system-node"></div>');
				$(nodeWidget).html('<div class="title">' + nodeData.title + '</div>');

				// Attach controller
				// -----------------
				ObjectController.attach({'permanent' : true, 'widget' : nodeWidget, 'id' : nodeData.id, 'class' : 'node'});

				// Open editor
				// -----------
				$(nodeWidget).dblclick(function() {
					API.Objects.action({'action' : 'edit', 'class' : 'node', 'id' : nodeData.id});
				});

				// Append to widget
				// ----------------
				$(manager.systemNodesPanel.widget).append(nodeWidget);

			})
		}


	},

	// Open node
	// -------------
	openNode : function(nodeID) {
		var manager = this;
		manager.nodeID = nodeID;
		manager.update();
	},

	// Fill table
	// ----------
	updateViewer : function()  {

		// This
		// ----
		var manager = this;

		// Clear viewer panel
		// ------------------
		$(this.viewerPanel).empty();

		// Select viewer component
		// -----------------------
		var viewerComponent = 'default';
		if (this.viewerComponent != null && Components.get('nodeViewer', this.viewerComponent) != null) {
			viewerComponent = this.viewerComponent;
		}
		// Create viewer and add to window
		// -----------------------------
		this.viewer = Components.get('nodeViewer', viewerComponent).getInstance({
			'manager' : this,
			'nodeID' : this.nodeID
		});

		if (this.mode != null) manager.viewer.setMode(this.mode);
		if (this.type != null) manager.viewer.setType(this.type);

		$(this.viewer.widget).appendTo(this.viewerPanel.widget);


	},

	// Selection complete
	// ------------------
	selectionComplete: function () {

		var result = [];
		var manager = this;

		// Form correct selection set
		// --------------------------
		$.each(this.selectedObjects, function(index, value) {
			result.push({'class' : 'node', 'id' : index});
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
		if (this.selectMode == true && this.multiselect == false) this.selectedObjects = {};
		this.selectedObjects[id] = true;
	}

});


// Default node viewer
// -------------------
Component.register({
	'type' : 'nodeViewer',
	'id' : 'default',


	// Viewer constructor
	// ------------------
	'constructor' : function(args) {

		// This
		// ----
		var viewer = this;

		// Set manager's object
		// --------------------
		this.manager = args.manager;
		this.nodeID = args.nodeID;

		// Set display options
		// -------------------
		this.pagesCount = 0; // Count of pages
		this.page = (args.page != null) ? args.page : 0; // Current page
		this.sort = {}; // Sort columns
		this.tableSort = null; // Sort columns
		this.type = null; // Selected type
		this.tableFormat = []; // Table format
		this.tableData = []; // Data to generate table
		this.filters = {}; // Set of the filters
		this.mode = 'tree'; // Mode of viewer. tree | list

		// Set widgets
		// -----------
		this.widget = $('<div class="node-viewer">' +
			'<div class="search-box-container"></div>' +
			'<div class="nodes-table"></div>' +
		'</div>');

		// Subwidgets
		// ----------
		this.searchBoxWidget = $(this.widget).find(".search-box-container");
		this.tableWidget = $(this.widget).find(".nodes-table");

		// Listen for nodes updates
		// ---------------------------
		Global.addListener(['objectUpdate', 'objectDelete', 'objectCreate'], function(e) {
			if (e.data.class = 'node') viewer.update();
		});

		// Update
		// ------
		this.update();
	},

	// Update
	// ------
	'update' : function() {

		// This
		// ----
		var viewer = this;

		// Get data
		// --------
		API.action({
			'action' : '/module/apps/nodesManager/update',
			'data' : {
				'parent' : viewer.nodeID,
				'page' : viewer.page,
				'sort' : viewer.tableSort,
				'filters' : viewer.filters,
				'mode' : viewer.mode,
				'type' : viewer.type,
				'target' : viewer.target
			},
			'callback' : function(result) {

				// Update widgets
				// --------------
				if (result != null) {
					$.extend(viewer, result);
					viewer.updateWidget();
				}
			}
		});
	},

	// Update widget
	// -------------
	'updateWidget' : function() {
		this.updateTypes();
		this.updateTable();
	},

	// Update widget
	// -------------
	'updateTable' : function() {

		// This
		// ----
		var viewer = this;

		// Clear widget
		// ------------
		$(this.tableWidget).empty();

		// Add page navigation
		// -------------------
		if (this.pagesCount > 0) {

			// Create paginator
			// ----------------
			var paginator = new Flex.Paginator({ count : viewer.pagesCount, page : viewer.page});
			$(this.tableWidget).append(paginator.widget);

			// Listen click event
			// ------------------
			paginator.addListener('select', function(e) {
				viewer.page = e.data;
				viewer.update();
			});
		}

		// Add icons
		// ---------
		viewer.tableFormat.push({'id' : '@icons'});

		// Create nodes table
		// ---------------------
		this.table = new FlexTable({

			// Table data
			// -----------
			'columns' : viewer.tableFormat,
			'sort' : viewer.tableSort,
			'data' : viewer.tableData,

			// Select options
			// --------------
			'select' : viewer.manager.selectMode,
			'multiselect' : !(viewer.manager.selectMode == true &&  viewer.manager.multiselect == false),

			// Init row
			// --------
			'rowInitialize' : function() {

				var row = this;

				// Open button
				// ------------------
				if (viewer.mode == 'tree') {

					// Create open button
					// ------------------
					var openButton = new FlexButton({ 'mode' : 'icon', 'id' : 'open-node',
						'click' : function() {
							viewer.manager.openNode(row.data['_id']);
						}
					});

					// Add button
					// ----------
					$(row.cells['@icons'].widget).append(openButton.widget);
				}

				// Target button
				// -------------
				else if (viewer.mode == 'list') {

					// Create target buttons
					// ---------------------
					var targetButton = new FlexButton({ 'mode' : 'icon', 'id' : 'target',
						'click' : function() {
							viewer.mode = 'tree';
							viewer.target = row.data['_id'];
							viewer.filters = {};
							viewer.type = null;
							viewer.update();
						}
					});

					// Add to cell
					// -----------
					$(row.cells['@icons'].widget).append(targetButton.widget);
				}

				// Select event
				// ------------
				if (this.cells['@select'] != null) {
					this.cells['@select'].addListener('select', function() {
						viewer.manager.selectObject(row.data['_id']);
					});
				}



				// Add type icon
				//  ------------
				$(row.cells['title'].widget).prepend('<span class="node-icon" data-node-type="' + row.data['typeID']+ '"></span>');

				// Add cursor
				// ----------
				$(this.widget).css('cursor', 'pointer');

				//  Add controller
				// ---------------
				ObjectController.attach({
					'permanent' : true,
					'widget' : this.widget,
					'id' : this.data['_id'],
					'class' : 'node',
					'actions' : {
						'open' : {'title' : 'Открыть', 'click' : function(obj) {
							viewer.manager.openNode(row.data['_id']);
						}}
					}
				});

				// Open editor
				// -----------
				$(this.widget).dblclick(function() {
					API.Objects.action({'action' : 'edit', 'class' : 'node', 'id' : row.data['_id']});
				});

			}

		});

		// Add sort listener
		// -----------------
		this.table.addListener('sort', function(e) {
			viewer.tableSort = e.data;
			viewer.update();
		});

		// Вставка таблицы
		// ---------------
		$(this.tableWidget).append(this.table.widget);

	},

	// Update list of types
	// --------------------
	updateTypes: function(args) {


		var viewer = this;

		// Empty panel
		// -----------
		$(viewer.searchBoxWidget).html(
			'<div class="type-selector"></div>' +
			'<div class="search-box"></div>'
		);

		// Fill type selector
		// ------------------
		var typeSelectorValues = {};
		$.each(viewer.types, function(typeIndex, typeData) {
			typeSelectorValues[typeData['_id']] = typeData.title;
		});

		// Add selection input
		// -------------------
		var typeSelector = new UI.FormInputs.select({
			'format' : {
				'values' : typeSelectorValues,
				'allowEmpty' : true,
				'emptyText' : '--Любой тип--'
			},
			'value' : viewer.type
		});

		// Add listener
		// ------------
		typeSelector.addListener('change', function(e) {
			viewer.setType(e.data);
		});

		// Append type selector
		// --------------------
		$(viewer.searchBoxWidget).find(".type-selector").append(typeSelector.widget);

		// Search box
		// ----------
		var searchInput = new UI.FormInputs.text({
			'format': {'id': 'search', 'placeholder' : 'текст для поиска'},
			'value' : viewer.filters['@text'],


		});
		$(this.searchBoxWidget).find('.search-box').append(searchInput.widget);

		// Search event
		// ------------
		searchInput.addListener('change', function(e) {
			viewer.search(e.data);
		});

	},

	// Search
	// ------
	search : function(query) {

		// Set filters
		// -----------
		this.filters = {'@text' : query };

		// Force to change mode
		// --------------------
		this.mode = 'list';
		this.update();
	},

	// Set new mode
	// ------------
	setMode : function(mode) {
		this.mode = mode;
		this.filters = {};
		this.update();
	},

	// Change type
	// -----------
	setType : function(type) {

		var viewer = this;
		if (viewer.type == type) return;


		viewer.type = type;
		viewer.page = 0;
		viewer.update();
	}


});
