// Базовый прототип виджета
// -----------------------------
Widget = function(args) {
	this.state = args.state;
	this.widgetID = args.widgetID
	this.container = $(args.containerID);
	this.data = args.data;

	// Добавляем себя в глобальную коллекцию
	// ----------------------------
	Widgets.add(args.containerID, this);
}

// Основные функции виджетов
// -----------------------------
Widget.prototype = $.extend({

	// Визуализация нового состояния
	// -------------------------
	render : function() {},

	// Обновление информации
	// ------------------------
	update : function() {
		var widget = this;

		API.action({
			'action' : 'widget/get',
			'data'  : {
				'widgetID' : this.widgetID,
				'args' : this.state,
				'options' : {'update' : true}
			},
			'callback' : function(result) {
				widget.data = result;
				widget.render();
			}
		})

	}

})


Widgets = {

	// Коллекция все виджетов
	// -----------------
	collection : {},

	// Добавляем виджет в коллекицю
	// -----------------
	add : function(id, widget) {
		Widgets.collection[id] = widget;
	},

	// Удаляем виджет из коллекции и из контейнера
	// -----------------
	remove : function(id) {
		$('#' + id).container.remove();
		delete Widgets.collection[id];
	}

}