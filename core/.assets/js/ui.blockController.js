// Контроллер блока
// -----------------------------------
UI.BlockController = function(args) {
	
	var controller = this;

	this.title = args.title;
	this.id = args.id;
	this.widget = $(args.widget);

	$(this.widget).mouseover(function() {	$(this).addClass("page-element-active"); });
	$(this.widget).mouseout(function() { $(this).removeClass("page-element-active");	});

	// Если ядро стартовало, то инициализируем
	// ---------------------------------------
	if (Core.state.editMode == true) this.initialize();
	Global.addListener('editModeOn', function() { controller.initialize(); })
	Global.addListener('editModeOff', function() { controller.deinitialize(); })

}


// Функции для контроллера блоков
// -----------------------------------
UI.BlockController.prototype = $.extend({}, Events.prototype, {

	initialize : function() {
		$(this.widget).addClass('page-element-block');
	},

	deinitialize : function() {
		$(this.widget).removeClass('page-element-block');
	}
});
