Apps.MediaViewer = function(args) {
	// Инициализация приложения
	// ------------------------
	this.mediaID = args.mediaID;

	this.window = new Flex.Window({'title' : 'Просмотр файлов', 'width' : 'auto', 'class' : ['media-viewer', 'adminTools']});
	this.content = $('<div class="media-viewer-content"></div>').appendTo(this.window.widget);

	Application.call(this);	
	this.init();
	
}

// Основные функции приложения
// ---------------------------
Apps.MediaViewer.prototype = $.extend({}, Application.prototype, {
	appID : 'mediaViewer',

	// Иницилизация состояния
	// ----------------------
	init: function() {

		var manager = this;

		var data = API.action({
			'action' : '/module/apps/mediaViewer/init',
			'data' : { 'mediaID' : manager.mediaID},
			'callback' : function(result) {
					if (result.content != null) {					
						$(manager.content).append(result.content);

						// Вставка информации о файле
						// --------------------------
						var info = $('<div class="info"></div>').appendTo(manager.content);
						if (result.info != null) {
							$(info).append(result.info);
						}
					}
			}
		});

	}

})