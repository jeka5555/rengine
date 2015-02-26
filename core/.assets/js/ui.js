// Элементы интерфейса
// -------------------------------------
if (window.UI == undefined) UI = {}

$.fn.uiDraggable = function(args) {

	var object = this;

	// Если нужно, добавляем данные
	// ----------------------------------
	if (args.data != undefined) $(this).data(args.data);

	// Инициализация типа
	// ----------------------------------
	if (args.types != undefined) typesList = args.types;
	else if (args.type != undefined) typesList = [args.type];
	else typesList = [];

	// Стандартное оформление
	// ----------------------------------
	$(this).draggable({
		'distance' : 5,
		'cursor' : 'pointer',
		'appendTo' : 'body',
		'helper' : function() {
			var helper = $(this).clone();
			$(helper).addClass("dragObject");
			return helper;
		},
		'zIndex': 2700,
		'opacity' : 0.8
	});

}

// Объект с поддержкой дропов по классам
// -------------------------------------
$.fn.uiDroppable = function(args) {

	$(this).droppable({
		greedy : true,
		drop : function(e, ui) {
			var object = ui.draggable;

			// Определяем какие типы объектов есть у бросаемого
			// ------------------------
			var typesList = [];
			if ($(object).data('typesList') != undefined) typesList = $(object).data('typesList');
			else if ($(object).data('type')) typesList = [$(object).data('type')];


			// Если тип объекта есть в типах, выполняем функцию
			// ------------------------
			for (var typeIndex in typesList) {
				var type = typesList[typeIndex];
				if (args.type != undefined) {
					if (args.type[type] != undefined) {
						args.type[type]($(object).data(), e, ui);
					}
				}
			}

			// Общий обработчик
			// ------------------------
			if (args.drop != undefined) args.drop($(object).data(), e, ui);
		}
	});

}

// Система вывода ошибок
// =====================================================================================
UI.error = function(args) {

	// Создаем каркас
	// ---------------------------------
	var errorWidget = $('<div class="errorsWidget"></div>');

	// Добавляем все ошибки
	// ---------------------------------
	if (args.errors != undefined && args.errors.length > 0) {

		for (var errorIndex in args.errors) {
			$(errorWidget).append('<div class="error">' + args.errors[errorIndex].text + '</div>');
		}
	}

	// Задаем название
	// ---------------------------------
	var title = 'Сообщения об ошибках';
	if (args.title != undefined) title = args.title;

	// Показываем диалог
	// ---------------------------------
	$(errorWidget).dialog({
		'autoOpen' : true,
		'dialogClass' : 'errorPopup',
		'modal' : true,
		'buttons' : [
			{ 'text' : 'Закрыть', 'click' : function() {$(this).dialog('close');} }
		],
		'title' : title
	});

}


// Инструменты объекта
// =======================================================
UI.ObjectTools = function(args)  {

	var objectTools = this;
	this.data = args;
	this.widget = $('#' + args.toolsWidgetID);	
	var toolsWidget = this.widget;

	// Получаем идентификатор и класс
	// -------------------------------
	var objectID = $(this.widget).attr("data-objectID");
	var objectClass = $(this.widget).attr("data-objectClass");

	// Если задан родительский элемнет
	// -------------------------------
	if (args.widgetID != undefined) {
		var parentWidget = $(args.widgetID);
		this.parentWidget = parentWidget;

		// Перепозиционируем
		// -------------------------------
		if ($(parentWidget).css('position') == "static") {
			$(parentWidget).css('position', 'relative');
			$(toolsWidget).css({'position' : 'absolute', 'top' : 0, 'right' : 0});
		}

		$(toolsWidget).data({ 'type' : 'object', 'objectClass' : objectClass, 'objectID' : objectID});


		$(toolsWidget).FlexDraggable({
			'contexts' : {
				'object' : {'class' : objectClass, 'id' : objectID }
			}
		});

		// Инструменты делаем видимым только при наведении
		// -------------------------------
		$(toolsWidget).css({ opacity : 0 });
		$(parentWidget).mouseenter(function() { $(toolsWidget).css({ opacity : 1});}).
		mouseleave(function() {	$(toolsWidget).css({ opacity : 0}); });

		// При дропе активируем drop
		// -------------------------------
		$(parentWidget).droppable({
			greedy : true,
			drop : function(e, ui) {

				var object = ui.draggable;

				// Обрабатываем объекты
				// ------------------------
				if ($(object).data('type') == 'object') {	
				    var sourceClass = $(object).data('objectClass');
				    var sourceID = $(object).data('objectID');

					// Ищем в дропах
					// --------------------
					if (objectTools.data.drops != undefined) {
							
						if (objectTools.data.drops[sourceClass]) {
							console.log('Найден совпадение в дропах для ' + sourceClass);
							API.Objects.drop({'target' : {'class' : objectClass, 'id' : objectID}, 'source' : {'class' : sourceClass, 'id' : sourceID }})
						}
					}
				}
			}
		});
	

	}

	// Удаление
	// -------------------------------
	$(this.widget).find(".action.delete").click(function() {
		if (confirm('Вы действительно хотите удалить объект?')) {
			API.Objects.delete({'class' : objectClass, 'query' : {'_id' : objectID}});
			Global.callEvent("updateObject", {"objectClass" : objectClass});
		}
	});

	// Редакирование
	// --------------------------------
	$(this.widget).find(".action.edit").click(function() {
		new UI.ObjectEditor({'class' : objectClass, 'id' : objectID});
	});
}


