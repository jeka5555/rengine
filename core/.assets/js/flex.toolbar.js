// Динамическая менюшка
// ----------------------------------
Flex.Toolbar = function(args) {

	// Инициализация
	// ------------------------------
	if (args == undefined) args = {};
	var toolbar = this;
	Events.init(this);
	this.flexClass = 'Flex.Toolbar';

	// Переменные
	// ------------------------------
	this.isCompact = safeAssign(args.isCompact, true);
	this.buttonMode = safeAssign(args.buttonsMode, "text");
	this.mode = safeAssign(args.mode, "prepend");
	this.parent = safeAssign(args.parent);
	this.isInvisible = safeAssign(args.isInvisible, false);
	this.hideForChildren = safeAssign(args.hideForChildren, false);
	this.class = safeAssign(args.class, 'flex-toolbar');
	this.exceptChildren = args.exceptChildren;
	this.side = safeAssign(args.side, 'right');
	this.elements = [];

	// Создаем виджет
	// ------------------------------
	this.widget = $('<div></div>').addClass(this.class);

	// Add class
	// ---------
	if (args.htmlClass != null) {
		$(this.widget).addClass(args.htmlClass);
	}

	// Text align
	// ----------
	if (args.align != null) $(this.widget).css('text-align', args.align);

	if (this.isInvisible) $(this.widget).hide();

	// Если у нас есть в аргументах кнопки, вставлям
	// ------------------------------
	if (args.elements != undefined) {
		$.each(args.elements, function(index, element) {

			if (element.type == 'text') toolbar.appendText(element);
			else if (element.type == 'button') toolbar.appendButton(element);
			else if (element.type == 'span') toolbar.appendSpan(element);
			else if (element.type == 'widget') toolbar.appendWidget(element);

		});
	}

	// Делаем привязку
	// -------------------------------
	if (args.parent) {
		this.attach({ 'parent' : args.parent});
	}

}

// Flex.Toolar
// -----------------------------------
Flex.Toolbar.prototype = $.extend({}, Events.prototype, {


	// Clear toolbar content
	// ---------------------
	clear : function() {
		this.elements = [];
		$(this.widget).empty();
	},


	// Отсоединяемся
	// -------------------------------
	detach : function() {
		$(this.widget).detach();
	},

	// Сокрытие панели
	// ---------------------------------
	hide : function() {
		$(this.widget).hide();
		this.callEvent('hide');
	},

	// Отображение панели
	// ------------------------------
	show : function() {
		$(this.widget).show();
		this.callEvent('show');
	},

	// Привязка меню к элементу
	// -------------------------------
	attach : function(args) {

		var parent = $(args.parent);
		var toolbar = this;

		// Реакция на наведене
		// ----------------------------
		if (this.isInvisible == true) {
			toolbar.hide();

			// При наведении показываем
			// -------------------------
			$(parent).mouseenter(function() { toolbar.show();});

			// Если мыши нет, скрываем
			// -------------------------
			$(parent).mouseout(function() { toolbar.hide(); });

			// Наведение - показываем
			// -------------------------
			$(parent).mouseover(
				function(e) {
					if (e.currentTarget != parent) {
						e.stopPropagation();
						e.stopImmediatePropagation();
						toolbar.show();
					}
				}
			)


		}

		// Прикрепление
		// ----------------------------
		switch (this.mode) {

			// Добавляем в конец
			// -------------------------
			case "append":
				$(parent).append(this.widget);
				break;

			// Добавляем в начало
			// --------------------------
			case "prepend":
				$(parent).prepend(this.widget);
				break;


			// Приклеиваем к элементу
			// ------------------------
			default:
				var parentPosition = $(parent).position();


				// Задаем позицию видежта
				// ---------------------
				$(this.widget).css({ 'position' : 'absolute', 'top' : 0, 'z-index' : 1});
				if (this.side == 'left') $(this.widget).css({ 'left' : 0 });
				else $(this.widget).css({ 'right' : 0 });

				// Гарантируем для родителя правильный position
				// ---------------------
				if ($(parent).css('position') == 'static' || $(parent).css('position') == '') {
					$(parent).css("position", "relative");
				}

				// Добавляемся в начало
				// ---------------------
				$(parent).prepend(this.widget);
				break;

		}

		$(parent).prepend(this.widget);
	},

	// Добавить кнопку
	// -------------------------------
	appendButton : function(buttonData) {

		var button = new FlexButton(buttonData);
		$(this.widget).append(button.widget);

		// Add with ID
		// -----------
		if (buttonData.id != null) {
			this.elements[buttonData.id] = button;
		}
	},

	// Добавить спан
	// -------------------------------
	appendSpan : function(span) {
		$('<span class="span"></span>').appendTo(this.widget);
	},

	// Добавить обычный текст
	// -------------------------------
	appendText : function(args) {
		$('<span class="flex-toolbar-text">' + args.text + '</span>').appendTo(this.widget);
	},

	// Добавить виджет
	// -------------------------------
	appendWidget : function(args) {
		$('<span class="flex-toolbar-widget"></span>').append(args.content).appendTo(this.widget);
	},

	// Открыть в полном экране
	// -------------------------------
	expand : function() {
	},

	// Собрать обратно
	// --------------------------------
	compact : function() {
	}

});
