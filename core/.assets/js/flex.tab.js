// Flex.Tab
// -------------------------------------
Flex.Tab = function(args) {

	// Инициализация
	// ------------------------------
	if (args == undefined) args = {};
	var tab = this;
	Events.init(this);
	this.flexClass = 'Flex.Tab';

	// Переменные
	// -----------------------------
	this.panel = args.panel;
	this.title = args.title;
	this.isClosed = safeAssign(args.isClosed, false);
	this.isDeletable = safeAssign(args.isDeletable, true);

	// Создаем виджет
	// -----------------------------
	this.widget = $('<div class="flex-tab"> \
		<div class="flex-tab-heading"><span class="title"></span><span class="controls"></span></div> \
		<div class="flex-tab-content"></div> \
	</div>');

	if (args.class != null) $(this.widget).addClass(args.class);

	this.content = $(this.widget).children(".flex-tab-content");

	// Если можно закрывать, то добавляем кнопку
	// -----------------------------
	if (args.isDeletable == true)
		$('<span class="miniButton close"></span>')
			.appendTo(
				$(this.widget)
				.children(".flex-tab-heading")
				.find(".controls")
			)
			.click(function() {
				tab.remove();
			})

	// Сворачивание
	// -----------------------------
	$(this.widget).children(".flex-tab-heading").click(function() {
		if (tab.isClosed) tab.open(); else tab.close();
	});

	// Если есть название, ставим
	// -----------------------------
	if (args.title !== undefined) this.setTitle(args.title);
	if (args.isClosed == true) this.close();
	
}

Flex.Tab.prototype = $.extend({}, Events.prototype, {

	// Задать название
	// -----------------------------
	setTitle : function (title) {
		$(this.widget).find(".flex-tab-heading .title").html(title);
	},

	// Удаление панели
	// -----------------------------
	remove : function() {
		if (this.isDeletable == false) return;
		this.callEvent('remove');
		$(this.widget).remove();
	},

	// Разворачиваем
	// ------------------------------
	open : function() {
		$(this.widget)
			.removeClass('closed')
			.children(".flex-tab-content").slideDown();
		this.isClosed = false;
		this.callEvent('open');
	},

	// Закрытие панели
	// ------------------------------
	close : function() {
		$(this.widget)
			.addClass('closed')
			.children(".flex-tab-content").slideUp();
		this.isClosed = true;
		this.callEvent('close');
	}
	
});
