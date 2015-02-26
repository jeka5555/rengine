// Окно для добавляения файлов из сервисов
// ---------------------------------------
Apps.MediaManager.ServicePopup = function(args) {

	// Инициализация
	// -------------
	var servicePopup = this;
	Application.call(this);
	this.window = new Flex.Window({ 'title' : 'Вставка медиа из внешних сервисов', 'class' : 'external-media-selector', 'width' : 500, 'height' : 'auto', 'minHeight' : 200});

	// Опции
	// ------
	if (args == null) args = {};
	this.folderID = args.folderID;
	this.upload = args.upload;

	// Поля и кнопки выбора
	// --------------------
	this.input = $('<input type="text" placeholder="Введите ссылку из внешнего источника" />').appendTo(this.window.widget);
	this.select = $('<input type="button" value="ОК" />').appendTo(this.window.widget);
	this.infoWidget = $('<div class="media-info" />').appendTo(this.window.widget);

	// Определение типа ссылки и id
	// ----------------------------
	this.url = null;
	this.mediaData = null;

	// При смене поля - реинициализация
	// --------------------------------
	$(this.input).change(function() {
	
		var url = $(this).val();
		var mediaData = { type: 'url', id: null, info: 'Тип ссылки не определен' };
		mediaData.url = url;

		// Проверка на youtube
		// ------------------
		if (/youtube\.com/im.test(url)) {
			mediaData.id = url.match(/\?v\=([a-zA-Z0-9\-\_\&]*)$/im)[1];
			mediaData.type = "youtube";
			mediaData.info = "Ссылка с <em>YouTube</em>, ID: <b>" + mediaData.id + "</b>";
		}
		if (/youtu\.be/im.test(url)) {
			mediaData.id = url.match(/([a-zA-Z0-9\-\_\&]*)$/im)[1];
			mediaData.type = "youtube";
			mediaData.info = "Ссылка с <em>YouTube</em>, ID: <b>" + mediaData.id + "</b>";
		}


		// Проверка на vimeo
		// ------------------
		if (/vimeo\.com/im.test(url)) {
			mediaData.id = url.match(/vimeo\.com\/([0-9]*)$/im)[1];
			mediaData.type = 'vimeo';
			mediaData.info = "Ссылка с <em>Vimeo</em>, ID: <b>" + mediaData.id + "</b>";
		}

		// Завершение работы
		// -----------------		
		$(servicePopup.infoWidget).html(mediaData.info);
		servicePopup.mediaData = mediaData;

	});

	// При клике на ОК - отправляем
	// ----------------------------
	$(this.select).click(function() {
		if (servicePopup.upload == true) servicePopup.uploadMedia();
		else {
			servicePopup.callEvent('ready', servicePopup.mediaData );
			servicePopup.close();
		}
	});

};

Apps.MediaManager.ServicePopup.prototype = $.extend({}, Application.prototype, {

	// Загрузка полученного ресурса на сервер
	// --------------------------------------
	uploadMedia: function() {

		var servicePopup = this;
	
		if (this.mediaData.id != null || this.mediaData.url != null) {

			// Если есть папка для загрузки, добавляем
			// ----------------
			if (servicePopup.folderID != null) this.mediaData.folderID = this.folderID;	
			// Операция вставки
			// ----------------
			API.Objects.classAction({
				'action' : 'createObject',
				'class' : 'media',
				'data' : this.mediaData,
				'callback': function(e) {
					servicePopup.callEvent('upload', servicePopup.mediaData);
					servicePopup.close();
				}
			})
		}
	}
})

