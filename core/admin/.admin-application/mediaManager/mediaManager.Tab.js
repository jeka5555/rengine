// Табы
// -----------------------------------
Apps.MediaManager.Tab = function(args) {

	// Инициализация
	// -------------
	var tab = this;
	Events.init(this);

	// Options
	// -------
	this.folderID = null;
	if (args.folderID != null) this.folderID = args.folderID;

	this.manager = args.manager;
	this.title = safeAssign(args.title, "Без названия");
	this.folders = [];
	this.media = [];
	this.path = [];

	// Widget
	// ------
	this.widget = $('<div></div>');

	// Дерево
	// ------
	this.treePanel = new Flex.Panel({'class' : 'nodes-manager-tree'});
	$(this.treePanel.widget).appendTo(this.widget);

	// Остальные блоки
	// ---------------
	this.contentPanel = new Flex.Panel({'class' : 'nodes-manager-content'});
	$(this.contentPanel.widget).appendTo(this.widget);

	// Блок для вывода папок текущего уровня
	// -------------------------------------
	this.foldersContent = $('<div class="foldersContent"></div>').appendTo(this.treePanel.widget);
	this.mediaContent = $('<div class="mediaContent"></div>').appendTo(this.contentPanel.widget);

	// Обновление
	// --------------------
	Global.addListener('mediaFolderUpdate', function() { tab.reload(); });
	Global.addListener('mediaUpdate', function() { tab.reload(); });


	// В таб можно складывать файлы и папки
	// --------------------------
	$(this.contentPanel.widget).FlexDroppable({
		'contexts' : {
			'object' : function(data) {
				switch (data.class) {

					// Складываем папку
					// ----------------
					case 'mediaFolder' :

						// Ignore self
						// -----------
						if (data.id != tab.folderID) {

							API.Objects.action({
								'action' : 'update',
								'class' : 'mediaFolder',
								'id' : data.id,
								'data' : {'parentID' : tab.folderID },
								'callback' : function() {
									Global.callEvent('mediaFolderUpdate');
								}
							})
						}
						break;

					// Складываем медиа
					// ----------------
					case 'media':

						API.Objects.action({
							'class' : 'media',
							'action' : 'update',
							'id' : data.id,
							'data' : { 'folderID' : tab.folderID },
							'callback' : function() {
								Global.callEvent('mediaFolderUpdate');
							}
						});

						break;
				}
			}
		}
	});

	// Reload
	// ------
	this.reload();

};

Apps.MediaManager.Tab.prototype = $.extend({}, Events.prototype, {

	// Перезагрузка
	// --------------------------
	reload : function() {
		var tab = this;
		var data = API.action({
			'action' : '/module/apps/mediaManager/getFolderContent',
			'data' : { 'folderID' : tab.folderID},
			'callback' : function(result) {
				if (result != null) {
					tab.folders = result.folders;
					tab.media = result.media;
					tab.path = result.path;
				}
				tab.update();
			}
		});
	},


	// Создание подпапки
	// --------------------------
	createSubfolder : function() {

		var tab = this;

		// Рисуем окна
		// -----------
		var dialog = new Flex.Dialog({
			'class' : 'query',
			'title' : 'Создание папки',
			'text' : 'Введите имя папки',
			'complete' : function(folderName) {
				var data = API.Objects.classAction({
					'action' : 'createObject',
					'class' : 'mediaFolder',
					'data' : { 'parentID' : tab.folderID,'title' : folderName },
					'callback' : function(result) {
						Global.callEvent('mediaFolderUpdate', {'folderID' : tab.folderID});
					}
				});
			}
		});
	},


	// Обновление контента
	// ----------------------------
	update : function() {

		var tab = this;

		// Чистим контент файлов
		// ---------------------
		$(tab.foldersContent).empty();
		$(tab.mediaContent).empty();

		// Генерация дерева
		// ----------------
		this.resourceTree = new Flex.MobileTree({
			'children' : tab.folders,
			'path' : tab.path,

			// Расширение
			// ----------
			'extendPath' : {
				'click' : function() {
						tab.folderID = this.data['id'];
						tab.callEvent('changeFolder', {'folderID' : this.data['id']});
				}
			},
			'extendNode' : {

				// Клик открывает новую папку
				// ----------------------------
				'click' : function() {
						tab.folderID = this.data['_id'];
						tab.callEvent('changeFolder', {'folderID' : this.data['_id']});
				},
				'initialize' : function() {

					// В таб можно складывать файлы и папки
					// --------------------------
					$(this.widget).FlexDroppable({
						'contexts' : {
							'object' : function(data) {

								switch (data.class) {
									case 'mediaFolder' :

										API.Objects.action({
											'action' : 'update',
											'class' : 'mediaFolder',
											'id' : data.id,
											'data' : {'parentID' : this.data['_id'] },
											'callback' : function() {
												Global.callEvent('mediaFolderUpdate');
											}
										});
										break;

									// Складываем медиа
									// ----------------
									case 'media':

										API.Objects.action({
											'class' : 'media',
											'action' : 'update',
											'id' : data.id,
											'data' : { 'folderID' : this.data['_id'] },
											'callback' : function() {
												Global.callEvent('mediaFolderUpdate');
											}
										});

										break;
									}
								}
							}
						});

					var folder = this;

					// Object controller
					// -----------------
   				    ObjectController.attach({'permanent' : true, 'widget' : this.widget, 'id' : this.data['_id'], 'class' : 'mediaFolder'});
				}
			}
		});

		//var folder = new Apps.MediaManager.Folder(tab.folders);
		//$(tab.foldersContent).append(folder.widget);

		$(tab.foldersContent).append(this.resourceTree.widget);


		// Сборка файлов
		// ---------------------------
		$.each(this.media, function(mediaIndex, mediaData) {
			var media = new Apps.MediaManager.Media(mediaData);

			// При клике вызываем событие выбора
			// ---------------------------------
			media.addListener('select', function(e) {
				tab.callEvent('selectMedia', {'mediaID' : e.data.mediaID});
			});

			// Добавляем файл
			// --------------
			$(tab.mediaContent).append(media.widget);
		});

	}
});


