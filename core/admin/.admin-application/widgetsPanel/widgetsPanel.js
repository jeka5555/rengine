// Панель со списком всех возможных виджетов
// -------------------------------------
Apps.WidgetsPanel = function() {

	// Инициализация окна
	// -----------------------------------
	Application.call(this);
	this.group = 'generic';
	this.window = new Flex.Window({ 'title' : 'Панель виджетов', 'width' : 400, 'class' : ['widgets-panel', 'adminTools']});
	this.load();

}

// Наследуем
// -------------------------------------
Apps.WidgetsPanel.prototype = $.extend({}, Application.prototype, {

	appID : 'widgetPanel',

	// Загрузка данных по объекту и формата
	// -------------------------------------
	load : function() {

		var widgetsPanel = this;
	
		// Запрашиваем нужный нам виджет
		// ---------------------------------
		API.action({
			'action' : '/module/apps/widgetsPanel/init',
			'callback' : function(result) {
				if (result != null) {
					widgetsPanel.widgets = result.widgets;
					widgetsPanel.types = result.types;
					widgetsPanel.init();
				}
			}
		});
	},


	// Инициализация приложения
	// --------------------------------
	init : function() {

		var widgetsPanel = this;

		// Элемент навигации по типам
		// --------------------------
		var typeValues = {};
		$.each(this.types, function(id, type) {	typeValues[type.id] = type.title; });

		this.selectButton = new UI.FormInputs.select({'format' : {'title' : 'Тип виджета', 'values' : typeValues}, 'value' : 'generic'});
		$(this.window.widget).append(this.selectButton.widget);

		// При смене меняем активный контент
		// ---------------------------------
		this.selectButton.addListener('change', function(e) {
			widgetsPanel.group = e.data;
			widgetsPanel.update();
		});		

		// Собираем виджеты
		// ----------------
		var widgetsContainer = new Flex.Panel({});
		this.widgetsContainer = widgetsContainer;
		$(this.window.widget).append(widgetsContainer.widget);


		widgetsPanel.update();

	},

	// Обновление
	// ----------
	update : function() {

		var widgetsPanel = this;
		$(this.widgetsContainer.widget).empty();

		$.each(this.widgets, function(widgetIndex, widgetData) {

			if (widgetsPanel.group != 'default') {
				if (widgetsPanel.group != widgetData.group) {
					return;
				}
			}

			// Элемент виджета
			// ---------------
			var widgetElement = $('<div class="widgets-panel-widget"></div>').html(widgetData.title);
			$(widgetElement).data('widgetID', widgetData.id);
			if (widgetData.color != null) $(widgetElement).css('background-color' , widgetData.color);

			// Делаем перетаскиваемым
			// ---------------------
			$(widgetElement).FlexDraggable({
				'contexts' : {
					'widgetType' : { 'type' : widgetData.id}
				}
			})

			// Добавляем в панельку
			// ---------------------
			$(widgetsPanel.widgetsContainer.widget).append(widgetElement);

		})

	}



});


