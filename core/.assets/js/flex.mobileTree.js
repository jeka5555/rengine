// Элемент дерева
// --------------------------
Flex.MobileTree = function(args) {

	// Инициализация
	// -------------
	var tree = this;
	Events.call(this);

	// Создаем контейнер
	// ----------------------
	this.widget = $('<div class="flex-mobileTree"></div>');
	this.pathWidget = $('<div class="flex-mobileTree-path"></div>').appendTo(this.widget);
	this.childrenWidget = $('<div class="flex-mobileTree-children"></div>').appendTo(this.widget);

	// Забираем данные
	// ---------------
	this.path = args.path;
	this.children = args.children;

	// Генерируем путь
	// ---------------
	if (args.path != null)
	$.each(args.path, function(pathIndex, path) {

		// Создаем элемент
		// ---------------
		var pathElement = new Flex.MobileTree.PathElement(path, args.extendPath);		

		// Добавляем разделитель
		// ---------------------
		$(tree.pathWidget).append(pathElement.widget).append('<span class="flex-mobileTree-path-elementDiv">/</span>')

	});
	
	// Удаляем последний разделитель
	$(tree.pathWidget).children('.flex-mobileTree-path-elementDiv').last().remove();
	
	// Если есть потомки, начинаем делать вставку
	// ----------------------
	if (args.children != null)
	$.each(args.children, function(childIndex, child) {

		// Создание узла и добавление его в дерево
		// -------------------
		var childNode = new Flex.MobileTree.Node(child, args.extendNode);
		$(tree.childrenWidget).append(childNode.widget);

	});

}


Flex.MobileTree.prototype = $.extend({}, Events.prototype, {});


// Элемент узла пути
// -----------------
Flex.MobileTree.PathElement = function(args, extension) {

	var pathElement = this;
	this.data = args;

	// Создание виджета
	// ----------------
	this.widget = $('<div class="flex-mobileTree-path-element"></div>');  
	$(this.widget).html(args.title);
	
	// Добавляем HTML class, если для неактивных элементов пути
	if(args.disabled === true)
		$(this.widget).addClass('disabled'); 
	else {
		$(this.widget).bind('click', function(event) { pathElement.click( event );});
		$(this.widget).bind('dblclick', function(event) { pathElement.dblClick( event );});
 	}
 	
	// Добавляем расширение
	// --------------------
	if (extension != null) $.extend(this, extension);
}

Flex.MobileTree.PathElement.prototype = $.extend({}, Events.prototype, {	
	'click' : function() {},
	'dblClick' : function() {}
});



// Нода дерева
// --------------------------
Flex.MobileTree.Node = function(args, extension) {

	// Инициализация ноды
	// ------------------
   var node = this;
	Events.call(this);

	// Читаем данные
	// -------------
	this.data = args;

	// Создаем виджет
	// -----------------------
	this.widget = $('<div class="flex-mobileTree-node"></div>');
	$(this.widget).html(this.data.title);

	// Добавляем расширение
	// --------------------
	if (extension != null) $.extend(this, extension);

	// События
	// -------
	$(this.widget).bind('click', function(event) { node.click( event );});
	$(this.widget).bind('dblclick', function(event) { node.dblClick( event );});


	this.initialize();

}


Flex.MobileTree.Node.prototype = $.extend({}, Events.prototype, {

	
	// Общие методы
	// ------------
	initialize : function() {},

	// События мышки
	// -------------
	click : function() {},
	dblClick : function() {},
	drop : function() {},
	drag : function() {}

});