Apps.MediaManager = function(args) {

	// Инициализация
	// ------------------------------
	var manager = this;
	if (args == undefined) args = {};

	this.selectedMedia = safeAssign(args.selectedMedia, []);
	this.selectMode = args.selectMode;
	this.multiple = args.multiple;
	this.folderID = args.folderID;

	args.title = safeAssign(args.title, 'Диспетчер файлов');
	args.icon = safeAssign(args.icon, '/core/.admin/admin-application/mediaManager/icon.png');

	// Создание приложения и окна
	// --------------------------
	Application.call(this);
	this.window = new Flex.Window({ 'title' : args.title, 'class' : ['media-manager','adminTools'], 'width' : 600, 'minHeight' : 200, 'icon' : args.icon});

	// Формируем панель инструментов
	// -----------------------------
	var toolbar = this.window.windowToolbar;
	toolbar.clear();

	// Кнопка загрузки файлов
	// ----------------------
	toolbar.addButton({
		'id' : 'upload',
		'title' : 'Загрузить',
		'icon' : '/core/.assets/img/icons/upload.png',
		'click' : function() {
			var uploader = new UI.FileUploader({'folderID' : manager.folderID, 'showPreview' : false});
			uploader.open();
			uploader.addListener('complete', function() { manager.update(); });
		}
	});

	// Кнопка для вставки из сервисов
	// ------------------------------
	toolbar.addButton({
		'id' : 'addFolder',
		'title' : 'Создать папку',
		'icon' : '/core/.assets/img/icons/addFolder.png',
		'click' : function() {
			manager.filesTab.createSubfolder();
		}
	});

	// Кнопка для вставки из сервисов
	// ------------------------------
	toolbar.addButton({
		'id' : 'service',
		'title' : 'Вставка из сервисов',
		'icon' : '/core/.assets/img/icons/youtube.png',
		'click' : function() {
			var selectMediaWindow = new Apps.MediaManager.ServicePopup({ folderID: manager.folderID, upload: true});
			selectMediaWindow.addListener('upload', function(e) {
				manager.update();
			});
		}
	});



	// Рисуем контент
	// --------------
	manager.update();

}



// Прототип
// --------
Apps.MediaManager.prototype = $.extend({}, Application.prototype, {

	appID : 'mediaManager',

	// Обновление всей панели менеджера
	// --------------------------------
	update : function() {

		var manager = this;

		// Очистка контента
		// -----------------
		$(this.window.widget).empty();

		// Панель для отображения текущей папки
		// -------------------------------------
		this.filesPanel = $('<div class="media-manager-files-panel"></div>').appendTo(this.window.widget);

		this.filesTab = new Apps.MediaManager.Tab({ folderID : this.folderID });
		$(this.filesPanel).append(this.filesTab.widget);

		// Слушаем смену папки
		// -------------------
		this.filesTab.addListener('changeFolder', function(e) {
			manager.folderID = e.data.folderID;
			manager.update();
		});

		// Слушаем выбор файла
		// -------------------
		if (manager.selectMode == true) {
			this.filesTab.addListener('selectMedia', function(e) {
				manager.returnSelected(e.data.mediaID);
			});
		}

		// Кнопка для выбора
		// -------------------------------
		if (this.selectMode) {

			// Если режим выбора - создаем кнопку "выбрать"
			// -------------------------------
			var selectButton = new UI.FormInputs.button({'format' : {'title' : 'Выбрать'}});
			$(selectButton.widget).addClass("selectButton").appendTo(this.window.widget);

			// Событие
			// ----------------------------
			selectButton.addListener('click', function(e) {
				manager.close();
			});
		}

	},

	// Возврат медиа
	// ------------------------------
	returnSelected : function(mediaID) {
		this.callEvent('selectMedia', {'mediaID' : mediaID});
		this.close();
	}

});

