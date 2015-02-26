// ContextMenu
// -----------
Flex.ContextMenu = function(args) {

	// This
	// ----
	var contextMenu = this;

	// Options
	// -------
	this.parent = args.parent;

	// If menu already exits
	// ----------------------
	if ($(this.parent).data('contextMenu') != null) {

		// Menu
		// ----
		var menu = $(this.parent).data('contextMenu');

		// Add groups
		// ----------
		if (args.groups != null) {
			$.each(args.groups, function(groupIndex, group) {
				menu.addGroup(group);
			});
		}

		// Return menu
		// -----------
		return menu;
	}

	// New instance
	// ------------
	else {
		this.create(args);
	}

}

Flex.ContextMenu.prototype = $.extend({}, Events.prototype, {


	// Create menu
	// ----------
	create : function(args) {

		// This
		// ----
		var contextMenu = this;

		// Options
		// -------
		this.mode = safeAssign(args.mode, 'rightClick');
		this.groups = {};

		// Add default group
		// -----------------
		this.addGroup({'id' : 'default'});

		// Create widget
		// -------------
		Events.call(this);
		this.widget = $('<div class="flex-context-menu"></div>');

		// Add classes
		// -----------
		$(this.widget).css({'cursor' : 'pointer', 'z-index' : 20000});

		// Set menu for object
		// -------------------
		$(this.parent).data('contextMenu', this);

		// By click, remove menu
		// ---------------------
		this.addListener('click', function() {
			contextMenu.hide();
		});

		// Bind to event
		// -------------
		this.bind = safeAssign(args.bind, 'contextmenu');

		if (this.parent !== undefined) {
			$(this.parent).bind(this.bind, function(e) {
				e.preventDefault();
				contextMenu.show(e);
				return false;
			});
		}

		// Add groups
		// ----------
		if (args.groups != null) {
			$.each(args.groups, function(groupIndex, groupData) {
				contextMenu.addGroup(groupData);
			});
		}

		// Hide if cursor is out
		// ---------------------
		$(this.widget).mouseleave(function() {
			contextMenu.hide();
		});

	},

	// Remove
	// ------
	remove : function() {
		$(this.parent).data('contextMenu', null);
		$(this.widget).hide().detach().remove();
	},

	// Add group
	// ---------
	addGroup : function(group) {
		var group = new FlexContextMenuGroup(group);
		this.groups[group.id] = group;
		$(this.widget).append(group.widget);
	},

	// Add element
	// -----------
	addElement : function(group, child) {

		var menu = this;

		// Take group
		// ----------
		var groupElement = null;
		if (this.groups[group] == null) groupElement = this.groups['default'];
		else groupElement = this.groups[group];

		// No any groups
		// -------------
		if (groupElement == null) {
			console.log('Нет групп для вставки элементов');
			return;
		}

		// Append element
		// -------------
		groupElement.addElement(child);
	},

	// Hide
	// ----
	hide : function() {
		$(this.widget).hide().detach();
	},

	// Show menu
	// ---------
	show : function(e) {

		var contextMenu = this;

		$(this.widget).css('display', 'inline-block');
		$(this.widget).appendTo($("body")).show(0, function() {

			var top = e.clientY - 10;
			var left = e.clientX - 10;
			if (top > ($(window).height() - $(contextMenu.widget).height())) top = $(window).height() - $(contextMenu.widget).height() - 10;
			if (left > ($(window).width() - $(contextMenu.widget).width())) left = $(window).width() - $(contextMenu.widget).width() - 10;

			$(contextMenu.widget).css({'left' : left, 'top' : top, 'position' : 'fixed'});

		});

		$(this.widget).show();
	}
});


// Group
// -----
FlexContextMenuGroup = function(args) {

	var group = this;
	Events.init(this);

	// Widgets
	// --------
	this.widget = $('<div class="flex-context-menu-group"/>');
	this.titleWidget = $('<div class="title"></div>').hide().appendTo(this.widget);
	this.childrenWidget = $('<div class="children"></div>').appendTo(this.widget);

	// Options
	// ------
	this.elements = [];

	// Set title
	// ---------
	if (args.title != null) {
		this.setTitle(args.title);
	}

	// Elements
	// --------
	if (args.elements) {
		$.each(args.elements, function(elementIndex, elementData) {
			group.addElement(elementData);
		});
	}
};

// Add prototype
// --------------
FlexContextMenuGroup.prototype = $.extend({}, Events.prototype, {


	// Set title
	// ---------
	setTitle : function(title) {
		this.title = title;
		$(this.titleWidget).append(title).show();
	},

	// Elements
	// --------
	addElement : function(data) {
		var element = new FlexContextMenuElement(data);
		$(this.widget).append(element.widget);
		this.elements.push(element);
	}
});

// Element
// -------
FlexContextMenuElement = function(args) {

	// Init
	// ----
	Events.init(this);
	var element = this;

	// Options
	// -------
	this.icon = args.icon;
	this.title = safeAssign(args.title, "");
	this.elements = {};

	// Create widget
	// -------------
	this.widget = $('<div class="flex-context-menu-element"></div>');

	// Set title
	// ---------
	$(this.widget).append('<span class="title">' + this.title + '</span>');

	// On over, show children
	// ----------------------
	$(this.widget).mouseenter(function() {
		element.showChildren();
		$(element.widget).addClass('active');
	});

	// On leave, hide all
	// ------------------
	$(this.widget).mouseleave(function() {
		element.hideChildren();
		$(element.widget).removeClass('active');
	});

	// Click event
	// -----------
	if (args.click != null) {
		$(this.widget).bind('click', function() {
			args.click();
			element.hideChildren();
			$(element.widget).removeClass('active');
			element.callEvent('click');
		});
	}

	// Children container
	// ------------------
	$(this.widget).append('<span class="children"></span>');

	// Auto add elements
	// -----------------
	if (args.elements != null) {
		$(this.widget).addClass('has-children');
		$.each(args.elements, function(elementIndex, elementData) {
			element.addElement(elementData);
		});
	}

}

FlexContextMenuElement.prototype = $.extend({}, Events.prototype, {

	// Add element
	// -----------
	addElement : function(element) {

		thisElement = this;
		if (element.id == null) element.id = 'element' + Math.floor(Math.random() * 10000000);

		// Добавление элементов в зависимости от типа
		// ----------------------------
		switch (element.type) {

			// Разделитель
			// -----------------------
			case 'splitter':
				$(this.widget).children(".children").append('<div class="splitter"></div>');
				break;

			// Добавляем группу
			// ----------------
			case 'group':

				// Создаем группу
				// --------------
				var groupElement = new Flex.ContextMenuGroup(element);
				this.groups[element.id] = groupElement;
				$(this.widget).children(".children").append(groupElement.widget);

				return groupElement;

			// Обычный элемент
			// ---------------
			default:

				// Если есть группа куда добавлять элемент
				// ---------------------------------------
				if (element.group != null && this.groups[element.group] != null) {
					return this.groups[element.group].addChildren(element);
				}

				// Создание элемента
				// -----------------
				else {
					var subElement = new Flex.ContextMenuElement(element);
					this.elements[element.id] = subElement;
					$(this.widget).children(".children").append(subElement.widget);

					// Прослушиваем все элементы
					// ---------------------
					subElement.addListener('click', function() {
						thisElement.callEvent('click');
					});
				}

				return subElement;
				break;
		}
	},

	showChildren : function() {
		$.each(this.elements, function(elementIndex, elementData) {
			$(elementData.widget).show();
		});
	},

	// Спрятать дочерние варианты
	// --------------------------------
	hideChildren : function() {
		$.each(this.elements, function(elementIndex, elementData) {
			$(elementData.widget).hide();
		});
	}
});
