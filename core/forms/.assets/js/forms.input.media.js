// Медиа
// ======================================================================
UI.FormInputs.media = function(args) {

	// id папки
	this.folderID = safeAssign(args.format.folderID, null);
	this.folderPath = safeAssign(args.format.folderPath, null);

	var mediaInput = this;
    UI.FormInput.call(this, args);

	// Создаем элемент для выбора медиа
	// -----------------------------
	this.widget = $('<div> \
		<div class="preview"></div> \
		<div class="controls"></div> \
	</div>');

	this.initWidget();

	// Selector
	// --------
	if (args.format.noSelect != true) {
		$(this.widget).children(".controls").append('<input type="button" class="selectObject" value="Выбрать" />');
	}

	// Add uploader
	// ------------
	this.uploader = new UI.FileUploader({
		'multiple' : false,
		'showPreview' : false,
		'folderID' : this.folderID,
		"folderPath" : this.folderPath
	});

	$(this.uploader.widget).appendTo($(this.widget).children(".controls"));
	this.uploader.addListener('fileUploadComplete', function(e) {
		mediaInput.setValue(e.data);
		mediaInput.callEvent('change', {'value' : mediaInput.value});
	});


	// Выбор медиа
	// ------------------------------
	$(this.widget).find(".selectObject").click(function() {

		var selector = new Apps.MediaManager({ selectMode : true});
		selector.addListener('selectMedia', function(e) {
			if (e.data.mediaID != undefined) {
				mediaInput.value = e.data.mediaID;		
				mediaInput.callEvent('change', {'value' : mediaInput.value});
				mediaInput.updatePreview();
			}
		});
	});

	// Можно бросать медиа
	// --------------------------
	$(this.widget).FlexDroppable({
		'contexts' : {
			'media' : function(data) {
				mediaInput.value = data.mediaID;
				mediaInput.updatePreview();
			}
		}
	});

	// Если есть значение, обновляем
	// ------------------------------
	if (args.value != undefined && args.value != null) this.updatePreview();

};

UI.FormInputs.media.prototype = $.extend({}, UI.FormInput.prototype, {

	// Установка значения
	// -----------------------
	setValue : function(value) {
		this.value = value;
		this.updatePreview();
	},

	// Чтение значения
	// -----------------------
	getValue : function() {
		return this.value;
	},

	// Обновление превью изображения
	// ------------------------
	updatePreview : function() {

		var mediaInput = this;

		// Если медиа не задано, то облом
		// -------------------------------
		if (this.value == undefined) return false;

		$(this.widget).find(".preview").empty().append(preview);

		// Тянем превьюшку и добавляем ее
		// -------------------------------
		var preview = API.action({
			'action' : '/module/media/getCover',
			'data' : {'mediaID' : this.value},
			'callback' : function(data) {

				var preview = $(data.preview).appendTo($(mediaInput.widget).children(".preview"));

				var mediaMenu = new Flex.ContextMenu({
					'parent' : preview,
					'elements' : [
						{'text' : 'Удалить', 'click' : function() {
							mediaInput.value = undefined;
							$(mediaInput.widget).find(".preview").empty();
						}}
					]
				});
			}
		});
	}

});