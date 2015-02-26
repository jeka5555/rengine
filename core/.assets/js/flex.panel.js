// Flex.Panel
// ---------------------------------
Flex.Panel = function(args) {

	// Инициализация
	// ------------------------------
	if (args == null) args = {};
	var panel = this;
	Events.init(this);
	this.flexClass = 'Flex.Panel';

	// Переменные
	// ------------------------------
	this.tabs = [];

	// Создаем виджет
	// ------------------------------
	this.widget = $('<div class="flex-panel"></div>');

	if (args.title != null)
	this.titleWidget = $('<div class="flex-panel-title">' + args.title + '</div>').appendTo(this.widget);

	// Дополнительные опции
	// --------------------
	if (args.id != null)  { $(this.widget).attr("id", args.id);}
	if (args.class != null)  { $(this.widget).addClass(args.class);}
}

Flex.Panel.prototype = $.extend({}, Events.prototype, {

	// Добавить таб
	// -----------------------------
	addTab : function(args) {

		var tab = args.tab;
		tab.depth = safeAssign(args.depth, this.tabs.length);

		if (this.tabs[tab.depth] != undefined) {
			$(tab.widget).insertAfter(this.tabs[tab.depth].widget);
			$(this.tabs[tab.depth].widget).remove();
		}

		else {
			$(this.widget).append(tab.widget);
		}
		// Вставляем
		// ---------------------------
		this.tabs[tab.depth] = tab;

	},

	// Удаление табов
	// ------------------------------
	removeTab : function(tabIndex) {
	}
});
