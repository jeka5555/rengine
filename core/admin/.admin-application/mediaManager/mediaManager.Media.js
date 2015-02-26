// Представление медиа
// ----------------------------------
Apps.MediaManager.Media = function(args) {

	Events.init(this);
	var media = this;
	this.mediaID = args['_id'];
	this.title = safeAssign(args['title'], "Без названия");

	// Сам виджет
	// ---------------------------
	this.widget = $('<div class="media"><div class="preview"></div><div class="title"></div></div>'); 

	// Двойной клик открывает просмотр
	// -------------------------------
	$(this.widget).bind('dblclick', function() {
		var mediaID = this.mediaID;
		Apps.start('MediaViewer', {'mediaID' : media.mediaID } );
	});

	// Добавляем иконку если есть
	// -----------------------
	if (args.iconURI !== undefined && args.iconURI !== "") {    
		$(this.widget).find(".preview").append('<img class="type-icon" src="' + args.iconURI + '" />');
	}

	// Добавляем картинку если есть
	// -----------------------
	if (args.preview !== undefined && args.preview !== "") {
		$(this.widget).find(".preview").append(args.preview);
	}

	// Присоединение возможностей
	// --------------------------
	ObjectController.attach({'permanent' : true, 'widget' : this.widget, 'id' : media.mediaID, 'class' : 'media'});

	// Присваиваем название и ставим класс в соотвествие с типом
	// -----------------------	
	$(this.widget).find(".title").html(safeAssign(args.title, "Без названия"));
	$(this.widget).addClass(args.type);

	// Кликом - выбирается
	// -----------------------
	$(this.widget).click(function() {
		media.callEvent('select', {
			mediaID : media.mediaID,
			widget : media.widget
		});
	});

}

Apps.MediaManager.Media.prototype = $.extend({}, Events.prototype, {

	// Переименование медиа
	// ---------------------
	rename : function() {
		var media = this;
		var dialog = new Flex.Dialog({
			'class' : 'query',
			'value' : media.title,
			'text' : 'Введите новое имя',
			'complete' : function(newName) {
				API.Objects.update( { 'class' : 'media', 'query' : {'_id' : media.mediaID}, 'data' : {'title' : newName} } );
				$(media.widget).find(".title").html(newName);
				media.title = newName;
			}
		});
	},

	// Скачать файл
	// ----------------------
	download : function() {
		var frame = $('<iframe></iframe>');
		$(frame).attr("src", '/module/media/download/' + this.mediaID);
		$("body").append(frame);	

	}
});