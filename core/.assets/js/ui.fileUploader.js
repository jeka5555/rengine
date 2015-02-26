// Загрузчик файлов с мощной поддержкой сервера
// -----------------------------------------
UI.FileUploader = function(args) {

	if (args == null) args = {};
	// Инициализация виджета
	// -------------------------------------
	Events.init(this);

	var fileUploader = this;
	this.uploadedFiles = [];

	var inputID = 'fileUploaderInput' + Math.ceil(Math.random() * 100000);
	this.inputID = inputID;
	this.args = args;
	
	this.folderID = safeAssign(args.folderID, null);
	this.folderPath = safeAssign(args.folderPath, null);
   
	// Замещаем на красивый загрузчик
	// -------------------------------------
	this.widget = $('<div class="fileUploader"><form enctype="multipart/form-data" method="POST"> \
		<div class="file-uploader-button flex-button has-icon"> \
            <span class="icon helper"></span> \
            <span class="title"></span> \
		</div> \
		<div class="uploadArea"></div> \
		<div class="progress"></div> \
		<input name="fileHelper" id="' + this.inputID + '" type="file" multiple="true" /> \
		<div class="results"></div> \
	</div></form>');
   
  
	fileUploader.init(); 

}




// Настройки тут
// -------------------------------------------
UI.FileUploader.prototype = $.extend({}, Events.prototype, {

	// Настройки
	// ---------
	chunkSize : 1024 * 1024,
	uploadURI : '/module/media/upload',

	// Отрываем
	// --------
	open: function() {
		$(this.widget).find("input[name=fileHelper]").click();
	}


});


// Инициализация
// ---------------------------------
UI.FileUploader.prototype.init = function() {

	var fileUploader = this;
	
	this.uploadInput = $(this.widget).find("input[name=fileHelper]").get(0);

	// Если задан multiple == false отключаем мультизагрузку
	// --------------------------------------
	if (this.args.multiple == false) {
		$(this.widget).find("#"+ this.inputID).attr("multiple", false);
	}

    // Если задано название кнопки
    // --------------------------------------
    if (this.args.buttonTitle != null) {
        $(this.widget).find('.file-uploader-button .title').html(this.args.buttonTitle);
    }

	// Флаг просмотра превью
	// --------------------------------------
	if (this.args.showPreview == false) this.showPreview = false;
	else this.showPreview = true;

	// Забираем подэлементы
	// --------------------------------------
	this.progressWidget = $(this.widget).find(".progress");
	this.resultsWidget = $(this.widget).find(".results");

	// Клик
	// --------------------------------------
	$(this.widget).find('.file-uploader-button').click(function() {
		$(fileUploader.widget).find("input[name=fileHelper]").click();
	});

	// На загрузку
	// --------------------------------------
	$(fileUploader.widget).find("input[name=fileHelper]").change(function() {
		fileUploader.upload();
	});

	// Очередь загрузки файлов и чанков
	// --------------------------------------
	this.filesQueue = [];
	this.chunksQueue = [];

	// Общее значение прогресса
	// --------------------------------------
	this.totalProgress = 0;
	this.filesCount = 0;
	this.uploadedFilesCount = 0

	// Событие активируемое при окончании загрузки файла
	// ---------------------------------------
	this.addListener('startUploadFile', function(e) {
		$(fileUploader.progressWidget).show().animate({'opacity' : 1});
		fileUploader.updateProgress();
		fileUploader.uploadFile(fileUploader.filesQueue[0]);
	});

	// Событие при загрузке чанка
	// -----------------------------------------
	this.addListener('chunkUploadComplete', function(e) {

		// Удаляем один чанк
		// -------------------------------------
		fileUploader.chunksQueue.splice(0,1);

		// Если есть еще, запускаем следующий
		// --------------------------------------
		if (fileUploader.chunksQueue.length > 0) {
			fileUploader.uploadChunk(fileUploader.chunksQueue[0]);
		}

		// Загрузка файла завершена
		// --------------------------------------
		else {

			// Добавляем файл в список загруженных
			// ----------------------------------
			if (e.data.status == "mediaUploaded" && e.data.id != undefined) {
				fileUploader.uploadedFiles.push(e.data.id);
			}
			fileUploader.callEvent('fileUploadComplete', e.data.result.id);
			fileUploader.updateResults();
		
		}

	});


	// Событие завершения загрузки	
	// -----------------------------------------
	this.addListener('fileUploadComplete', function(e) {

		fileUploader.uploadedFilesCount ++;
		fileUploader.filesQueue.splice(0,1);

		// Если очередь пустая, то прячем прогресс
		// -----------------------------------
		if (fileUploader.filesQueue.length < 1) {
			$(fileUploader.progressWidget).animate({'opacity' : 0}, function() {
				$(this).hide();
			})

			// Вызов события завершения
			// -------------------------------
			fileUploader.callEvent('complete');
		}

		// Если нет, стартуем следующий
		// ----------------------------------
		else fileUploader.uploadFile(fileUploader.filesQueue[0]);

		// Обновляем прогресс загрузки
		// ----------------------------------
		fileUploader.updateProgress();

	});
}


// Просмотр загруженных файлов
// ---------------------------------
UI.FileUploader.prototype.updateResults = function() {

	// Если превью не показывать, то ничего не делаем
	// ------------------------------
	if (this.showPreview == false) return;

	var fileUploader = this;

	$(this.resultsWidget).empty();
	if (this.uploadedFiles.length < 1) return;

	// Забираем контент
	// -----------------------------
	var content = API.action({'action' : '/module/media/getUploaderPreview', 'data' : {'mediaList' : this.uploadedFiles}})

	// Добавляем удаление
	// -----------------------------
	$(content).find('.loadedMedia').each(function() {
		var mediaID = $(this).attr('data-mediaID');

		$(this).find('.miniButton.delete').click(function() {
			if (confirm('Вы хотите удалить данный файл')) {
				API.Objects.delete({'class' : 'media', 'query' : {'_id' : mediaID}});
				var fileIndex = fileUploader.uploadedFiles.indexOf(mediaID);
				console.log('index', fileIndex);
				if (fileIndex != -1 ) fileUploader.uploadedFiles.splice(fileIndex, 1);

				fileUploader.updateResults();
			}
		});
	});

	// Добавляем
	// -----------------------------
	$(this.resultsWidget).append(content);

}

// Обновление прогресса
// ---------------------------------
UI.FileUploader.prototype.updateProgress = function() {
	
	var fileUploader = this;
	
	// Виджет прогресса
	// ----------------------------
	this.progress = Math.round((100 / this.filesCount) * this.uploadedFilesCount);
	var progress =  this.progress + '%';

	var progress = $('<div class="progressBar"><span class="bar" style="width:' + progress + '"></span></div><span class="value">' + progress + '</span>');

	// Добавляем новое состояние прогресса
	// -----------------------------
	$(fileUploader.progressWidget).empty().html(progress);
}

// Обновление интерфейса
// --------------------------------------------
UI.FileUploader.prototype.updateUI = function() {
}

// Выгрузка одного чанка
// ------------------------
UI.FileUploader.prototype.uploadChunk = function(chunk) {

	// Инициализация объекта для передачи
	// -------------------
    var xhr = new XMLHttpRequest();
	var fileUploader = this;

	// Считываем данные чанка
	// --------------------
	if (chunk.file.slice != null) var data = chunk.file.slice(chunk.start, chunk.start + this.chunkSize, "application/octet-stream");
	else if (chunk.file.webkitSlice != null) var data = chunk.file.webkitSlice(chunk.start, chunk.start + this.chunkSize, "application/octet-stream");
	else var data = chunk.file.mozSlice(chunk.start, chunk.start + this.chunkSize, "application/octet-stream");
	
	// Ошибка
	// ------
	if (data == null) {
		console.error('Не поддерживается загрузка');
		return;
	}

	// Вешаем событие
	// --------------------
	xhr.onreadystatechange = function(e) {

		// Если завершена загрузка
		// ----------------
		if (xhr.readyState == 4) {
			var state = $.parseJSON(xhr.responseText);			
			fileUploader.callEvent('chunkUploadComplete', state);
		}
	};
	
	// Отправляем фрагмент
	// --------------------
	xhr.open("POST", this.uploadURI, true);
	xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	xhr.setRequestHeader("X-File-Name", encodeURIComponent(chunk.fileName));
	xhr.setRequestHeader("X-File-ID", chunk.fileID);
	if (fileUploader.folderID != null) xhr.setRequestHeader("X-Folder-ID", fileUploader.folderID);
	xhr.setRequestHeader("X-File-Part", chunk.chunkID);
	xhr.setRequestHeader("X-Blobs-Count", chunk.chunksCount);
	xhr.setRequestHeader("Content-Type", "application/octet-stream");
	xhr.setRequestHeader("REQUEST-MODE", "API");
	xhr.send(data);	

}

// Выгрузка одного файла
// ------------------------
UI.FileUploader.prototype.uploadFile = function(args) {

	// Определяем данные файла
	// --------------------
	var file = args;
	var fileName = file.name;
    var fileSize = file.size;

	// Случайный индентификатор отправки
	// ------------------------		
	var fileID = Math.round(Math.random()*1000000);

	// Количество чанков
	// ------------------------
	var chunksCount = Math.ceil(fileSize / this.chunkSize);

	// Помещаем чанки в очередь
	// ------------------------
	this.chunksQueue = [];
	for(var chunkID = 0; chunkID < chunksCount; chunkID++) {

		var chunkData = {
			'file' : file.file,
			'start' : chunkID * this.chunkSize,
			'chunkID' : chunkID,
			'fileID' : fileID,
			'fileName' : fileName,
			'chunksCount' : chunksCount
		}
			
		this.chunksQueue.push(chunkData);
	}

	// Запускаем очередь
	// ------------------------
	this.startChunksQueue();

}


// Запуск цепочки загрузки чанков
// ------------------------------
UI.FileUploader.prototype.startChunksQueue = function() {

	if (this.chunksQueue.length > 0) this.uploadChunk(this.chunksQueue[0]);
};

// Запуск загрузки
// ------------------------
UI.FileUploader.prototype.upload = function(args) {

	// Забираем список файлов
	// --------------------
	var fileUploader = this;
	var files = this.uploadInput.files;

	// Формируем очередь загрузки
	// ---------------------
	this.filesQueue = [];
	for(var i = 0, file; file = files[i]; ++i) {
		var fileData = {
			'file' : files.item(i),
			'name' : files.item(i).name,
			'size' : files.item(i).size,
			'type' : files.item(i).type
		};
		this.filesQueue.push(fileData);
	}

	// Задаем число файлов
	// ---------------------
	this.filesCount = this.filesQueue.length;
	this.uploadedFilesCount = 0;
	
	// Если передан путь до папки, то ищем на сервере и создаем папки на пути
	// ----------------------------
	if(this.folderPath != null) {
		API.action({
			'action' : '/module/media/folderPathProcess',
			'data' : { 'folderPath' : this.folderPath },
			'callback' : function(result) {
			
				// Устанавливаем id вновь созданной папки
				if(result != null) fileUploader.folderID = result;
			
				// Запускаем очередь загрузки
				// ---------------------
				fileUploader.callEvent('startUploadFile');
				
			}
		});
	} else {
		// Запускаем очередь загрузки
		// ---------------------
		fileUploader.callEvent('startUploadFile');
	} 



}