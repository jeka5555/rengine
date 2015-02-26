Apps.AccessEditor = function(args) {

	// Инициализация приложения
	// ------------------------
	this.mediaID = args.mediaID;

	this.window = new Flex.Window({'title' : 'Редактор прав доступа', 'width' : 'auto', 'class' : ['access-editor', 'adminTools']});
	this.content = $('<div class="access-editr-content"></div>').appendTo(this.window.widget);

	Application.call(this);	
	this.init();
	
}

// Основные функции приложения
// ---------------------------
Apps.AccessEditor.prototype = $.extend({}, Application.prototype, {
	appID : 'accessEditor',

	// Иницилизация состояния
	// ----------------------
	init: function() {

		var editor = this;

		var data = API.action({
			'action' : '/module/apps/accessEditor/init',
			'data' : { 'class' : editor.class, 'id' : editor.id},
			'callback' : function(result) {

			}
		});

	}

})