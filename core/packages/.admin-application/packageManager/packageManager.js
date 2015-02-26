Component.register({

	// Create component
	// ----------------
	'type' : 'settings-tab',
	'id' : 'packages',
	'title' : 'Пакеты',

	// Constructor
	// -----------
	'constructor' : function(args) {

		// Init
		// ----
		var manager = this;
		if (args == undefined) args = {};

		// Create widgets
		// --------------
		this.widget = $('<div class="packages-manager"></div>');
		this.container = $('<div class="container"/>').appendTo(this.widget);

		// Init
		// ----
		manager.init();


	},


	// Init application
	// ----------------
	init : function() {

		var manager = this;

		var data = API.action({
			'action' : '/module/apps/packageManager/init',
			'callback' : function(result) {
				if (result != null && result != false) {
					manager.data = result;
					manager.update();
				}
			}
		});

	},

	// Update UI
	// ---------
	update : function() {

		var manager = this;
		$(manager.container).html('');

		this.downloadModulesPanel = new Flex.Tab({ 'title' : 'Загруженные пакеты' });
		$(this.downloadModulesPanel.widget).appendTo(manager.container);
		this.updateDownloadModulesTable();


		//this.repositoryModulesPanel = new Flex.Tab({ 'title' : 'Репозиторий пакетов' });
		//$(this.repositoryModulesPanel.widget).appendTo(manager.container);
		//this.updateRepositoryModulesTable();

	},


	  // Update download Modules Table
	// ---------
	updateDownloadModulesTable : function() {

		var manager = this;

		// Проходимся по всем модулям, строим тулбар для каждого
		// ----------
		$.each(manager.data.downloadModules, function(key, value){

			// Add toolbar
			// --------------------
			var actionsPanel =  new Flex.Toolbar({'htmlClass' : 'class-actions-toolbar'});

			if(value.actions != null && value.actions.init == true)
				actionsPanel.appendButton({'id': 'init', 'class' : 'install-package', 'mode' : 'icon', 'title' : 'Инициализация пакета', 'click' : function(){
					manager.initPackage(value);
				} });

			if(value.actions != null && value.actions.uninstall == true)
				actionsPanel.appendButton({'id': 'delete', 'mode' : 'icon', 'class' : 'delete-package', 'title' : 'Удалить пакет', 'click' : function(){
					if(confirm("Вы уверены, что хотите удалить пакет безвозвратно?"))
						manager.uninstallPackage(value);
				} });

			value.buttons = actionsPanel.widget;
		});

		// Data table
		// ----------
		this.downloadModulesTable = new FlexTable({

			// Basic table settings
			// --------------------
			'sort': true,

			// Columns and data
			// ----------------
			'columns': [
				{type : 'image', id: 'icon', 'width' : 44, 'height' : 44 },
				{type : 'text', title : 'Название', listing : true, sortable : true, id : 'title' },
				{type : 'text', title : 'Описание', id : 'description' },
				{type : 'text', id: 'order', title: 'Порядок', sortable : true},
				{type : 'checkbox', css : {'text-align' : 'center', 'width' : '10px' }, title : 'Включено', id : 'enable',
					'change' : function(cell) {
						manager.enablePackage(cell.row.data);
					},
					'unchange' : function(cell) {
						manager.disablePackage(cell.row.data);
					},
				},
				{type : 'text', id : 'buttons', 'css' : {'white-space' : 'nowrap', 'text-align' : 'center'} },
			],
			'data': manager.data.downloadModules,
		});

    $(this.downloadModulesPanel.content).html(this.downloadModulesTable.widget);
	},


	// Update Repository Modules Table
	// ---------
	updateRepositoryModulesTable : function() {

		var manager = this;
		
		if(manager.data.repositoryModules == null) return false;

		// Проходимся по всем модулям, строим тулбар для каждого
		// ----------
		$.each(manager.data.repositoryModules, function(key, value){

			// Add toolbar
			// --------------------
			var actionsPanel =  new Flex.Toolbar({'htmlClass' : 'class-actions-toolbar'});

			if(value.actions != null && value.actions.install == true)
				actionsPanel.appendButton({'id': 'install', 'class' : 'download-package', 'mode' : 'icon', 'title' : 'Скачать пакет', 'click' : function(){ alert('Вызов функции установки'); } });


			value.buttons = actionsPanel.widget;
		});

		// Data table
		// ----------
		this.repositoryModulesTable = new FlexTable({

			// Basic table settings
			// --------------------
			'sort': true,

			// Columns and data
			// ----------------
			'columns': [
				{type : 'image', id: 'icon', 'width' : 44, 'height' : 44 },
				{type : 'text', title : 'Название', listing : true, sortable : true, id : 'title' },
				{type : 'text', title : 'Описание', id : 'description' },
				{type : 'text', id : 'buttons' }
			],
			'data': manager.data.repositoryModules,
		});

		$(this.repositoryModulesPanel.content).html(this.repositoryModulesTable.widget);
	},

	enablePackage : function(package) {

		var manager = this;
		
		var data = API.action({
			'action' : '/module/apps/packageManager/enablePackage',
			'data' : { 'id' : package.id },
			'callback' : function(result) {
			}
		});
	},

	disablePackage : function(package) {

		var manager = this;

		var data = API.action({
			'action' : '/module/apps/packageManager/disablePackage',
			'data' : { 'id' : package.id },
			'callback' : function(result) {
			}
		});
	},

	initPackage : function(package) {

		var manager = this;

		var data = API.action({
			'action' : '/module/apps/packageManager/initPackage',
			'data' : { 'id' : package.id },
			'callback' : function(result) {
				manager.init();
			}
		});
	},
	
	uninstallPackage : function(package) {

		var manager = this;

		var data = API.action({
			'action' : '/module/apps/packageManager/uninstallPackage',
			'data' : { 'id' : package.id },
			'callback' : function(result) {
				manager.init();
			}
		});
	}
	

});