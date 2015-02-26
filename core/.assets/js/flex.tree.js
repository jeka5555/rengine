// Элемент дерева
// --------------------------
Flex.Tree = function(args) {


	// Инициализация
	// -------------
	var tree = this;
	Events.call(this);

	
	// Создаем контейнер
	// ----------------------
	this.widget = $('<div class="flex-tree"></div>');

	// Если есть потомки, начинаем делать вставку
	// ----------------------
	if (args.children != null)
	$.each(args.children, function(childIndex, child) {

		// Создание узла
		// -------------
		var childNode = new Flex.TreeNode(child, args.extendNode);

		// Прослушка события выбора
		// ------------------------
		childNode.addListener('select', function(e) {
			$(tree.widget).find('.flex-tree-node').each(function() {
				if (e.data.node != $(this).data('tree-node')) $(this).removeClass('selected');
				else $(this).addClass('selected');
			});
		});

		// Добавляем виджет в дерево
		// -------------------------
		$(tree.widget).append(childNode.widget);

	});

}


Flex.Tree.prototype = $.extend({}, Events.prototype, {});


// Нода дерева
// --------------------------
Flex.TreeNode = function(args, extension) {

	// Инициализация ноды
	// ------------------
  var node = this;
	Events.call(this);

	// Читаем данные
	// -------------
	this.data = args;

	// Настройки
	// ---------
	this.isClosed = safeAssign(args.closed, false);

	// Создаем виджет
	// -----------------------
	this.widget = $('<div class="flex-tree-node"></div>');
	$(this.widget).data('tree-node', this);

	// Добавляем расширение
	// --------------------
	if (extension != null) {
		this.extension = extension;
		$.extend(this, extension);
	}

	// Добавляем название
	// -----------------------
	this.update();

}


Flex.TreeNode.prototype = $.extend({}, Events.prototype, {

	// Рисуем полный контент
	// ---------------------
	update : function() {

		// Генерация блока с названием
		// ---------------------------
		var titleContent = this.getTitleContent();
		if (titleContent != null) $(this.widget).append(titleContent);

		// Генерация блока с потомками
		// ---------------------------
		var childrenContent = this.getChildrenContent();
		if (childrenContent != null) $(this.widget).append(childrenContent);

		if (this.hasChildren() && this.isClosed) $(this.widget).addClass('closed');

		this.initialize();
	},


	// Выбор текущей ноды
	// ------------------
	select: function() {
		this.callEvent('select', {'node' : this});
	},

	// Генерация текста для ноды
	// --------------------------
	getTitleContent : function() {

		var node = this;

		// Создаем узел
		// ------------
		var titleWidget = $('<div class="flex-tree-node-heading"></div>');
		var closeButton  = $('<span class="flex-tree-node-close-icon"></span>');
		$(titleWidget).append(closeButton);

		// Установка контента
		// -----------------------
		if (this.data.html != null) $(titleWidget).append('<span class="flex-tree-node-title">' + this.data.html + '</span>');
		else if (this.data.title != null) $(titleWidget).append('<span class="flex-tree-node-title">' + this.data.title + '</span>');

		$(closeButton).bind('click', function(e) {
			node.toggle();
			e.stopImmediatePropagation();
			e.stopPropagation();
		});

		$(titleWidget).bind('click', function(event) { node.click( event );});
		$(titleWidget).bind('dblclick', function(event) { node.dblClick( event );});

		// Возвращаем
		// ----------
		return titleWidget;

	},

	// Построение контента ноды
	// ------------------------
	getChildrenContent : function() {

		var node = this;

		// Если есть потомки, вставляем
		// -----------------------
		if (this.data.children != null && this.data.children.length > 0) {

			$(this.widget).addClass('flex-tree-node-has-children');

			// Создаем контейнер
			// -----------------
			var childrenContainer = $('<div class="flex-tree-node-children"></div>');

			// Добавляем элементы
			// ------------------
			$.each(this.data.children, function(childIndex, child) {

				// Создание узла и добавление его в дерево
				// -------------------
				var childNode = new Flex.TreeNode(child, node.extension);

				// Прослушка на select
				// -------------------
				childNode.addListener('select', function(e) {
					node.callEvent('select', e.data);
				});

				// Добавляем
				// ---------
				$(childrenContainer).append(childNode.widget);

			});

			// Добавляем контейнер
			// -------------------
			return childrenContainer;
		}

	},

	// Общие методы
	// ------------
	initialize : function() {},

	// События мышки
	// -------------
	click : function() {},
	dblClick : function() {},
	drop : function() {},
	drag : function() {},

	// Проверка состояния
	// ------------------
	hasChildren : function() {
		if (this.data.children != null && this.data.children.length > 0) return true;
		return false;
	},

	// Манипуляция состоянием
	// ----------------------
	toggle : function() { if (this.isClosed) this.open(); else this.close(); },
	open : function() { this.isClosed = false; $(this.widget).removeClass('closed'); },
	close : function() { this.isClosed = true; $(this.widget).addClass('closed'); },


});