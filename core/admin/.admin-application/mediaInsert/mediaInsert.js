Apps.MediaInsert = function(args) {
	// Инициализация приложения
	// ------------------------

	var mediaInsert = this;
		
	Application.call(this);	
	
	this.window = new Flex.Window({'title' : 'Вставка файлов из Медиа', 'width' : '420', 'class' : ['media-insert', 'adminTools'], 'modal': true});
	this.content = $('<div class="media-insert-content"></div>').appendTo(this.window.widget);
	this.mainFormBlock = $('<div class="media-insert-mainForm"></div>').appendTo(this.content);
	this.addInFormBlock = $('<div class="media-insert-addInForm"></div>').appendTo(this.content);

	// Add save button
	// ---------------
	this.window.windowToolbar.addButton({
		'title' : 'Вставить',
		'click' : function() {
			mediaInsert.insert();
		}
	});

	this.init();
	
}

// Основные функции приложения
// ---------------------------
Apps.MediaInsert.prototype = $.extend({}, Application.prototype, {
	appID : 'MediaInsert',

	// Иницилизация состояния
	// ----------------------
	init: function() {

		var mediaInsert = this;

		mediaInsert.mainForm = new UI.Form({'format' : {
			'media' : {'title' : 'Файл для присоединения', 'type' : 'media'}
			}
		}); 
		
		mediaInsert.mainForm.inputs['media'].addListener('change', function(e){
		 	API.action({
				'action' : '/module/apps/mediaInsert/getMediaType',
				'data' : {'mediaID' : e.data.value },
				'callback' : function(type) {
					mediaInsert.type = type;
						
					// Если изображение
					if(type == 'image') {
					
						mediaInsert.addInForm = new UI.Form({'format' : {
							'width' : {'title' : 'Ширина', 'type' : 'number'},
							'height' : {'title' : 'Высота', 'type' : 'number'},
							'mode' : {'title' : 'Режим обрезки', 'type' : 'select', 'allowEmpty' : true, 'values' : {'contain' : 'Вместить', 'cover' : 'Заполнить'}}
							}
						});
					
								
					} else {
					
						mediaInsert.addInForm = new UI.Form({'format' : {
							'title' : {'title' : 'Название файла', 'type' : 'text'}
							}
						});
					
					}
					
					$(mediaInsert.addInFormBlock).html(mediaInsert.addInForm.form);
					
				}
			});	
			
		});
		
		$(mediaInsert.mainFormBlock).append(mediaInsert.mainForm.form); 

	},
	
	insert: function() {
	
		var mediaInsert = this;
		
		var dataMain = mediaInsert.mainForm.getValue();
	
		var dataAddInForm = mediaInsert.addInForm.getValue(); 
		
		dataAddInForm.mediaID = dataMain.media;
		
	 	API.action({
			'action' : '/module/apps/mediaInsert/getContent',
			'data' : {'mediaID' : dataMain.media, 'type' : mediaInsert.type, 'data' : dataAddInForm },
			'callback' : function(content) {	
			
				mediaInsert.close();
			
				if (content != null)	{
					mediaInsert.callEvent('insertMedia', {'content' : content});
				}
			}
		});	
		
	}

})