// Диалоги
// ---------------------------------
Flex.Dialog = function(args) {

	if (args == null) args = {}
	this.title = safeAssign(args.title, "Окно");

	// Инициализация окна
	// ------------------------
	Flex.Window.call(this, {
		'title' : this.title,
		'modal' : true,
		'width' : 400,
		'height' : 200
	});

	// В зависимости от класса вызываем метод
	// ------------------------
	$(this.widget).addClass("flex-dialog");
	this.class = safeAssign(args.class, "alert");
	this[this.class + "Init"](args);

};

Flex.Dialog.prototype = $.extend({}, Flex.Window.prototype, {

	// Выдача сообщения
	// ------------------------
	alertInit : function(args) {
	},

	// Вопрос
	// ------------------------
	queryInit : function(args) {

		var dialog = this;

		var content = $('<div class="adminTools"> \
			<div class="query"></div> \
			<div class="input"><input type="text" /></div> \
			<div class="buttons"><input type="button" class="ok" value="ОК"></div> \
		</div>');

		if (args.text != undefined) $(content).find(".query").html(args.text);
		if (args.value != undefined) $(content).find(".input input").val(args.value);

		$(content).find(".ok").click(function() {
			args.complete($(content).find(".input input").val());
			$(dialog.widget).remove();
		});
	
		$(content).find("input[type=text]").keyup(function(e) {
			if (e.which == 13) {
				args.complete($(content).find(".input input").val());
				$(dialog.widget).remove();
			}
		});


		$(this.widget).append(content);

	}


});
