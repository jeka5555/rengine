
$.fn.dontScrollParent = function()
{
	this.bind('mousewheel DOMMouseScroll',function(e)
	{

		var delta = e.originalEvent.wheelDelta || -e.originalEvent.detail;

		if (delta > 0 && $(this).scrollTop() <= 0)
			return false;
		if (delta < 0 && $(this).scrollTop() >= this.scrollHeight - $(this).height() - 20)
			return false;

		return true;
	});
}

// Окно
// ----------------------------------
Flex.Window = function(args) {

	// Инициализация
	// ------------------------------
	if (args == undefined) args = {};
	Events.init(this);
	this.flexClass = 'Flex.Window';
	this.isFixed = safeAssign(args.isFixed, true);

	var win = this;
	var dialog = this;

	// Переменные
	// --------------------------
	win.icon = safeAssign(args.icon, false);
	win.title = safeAssign(args.title, "Окно без названия");
	win.modal = safeAssign(args.modal, false);
	win.autoOpen = safeAssign(args.autoOpen, true);
	win.maximizable = safeAssign(args.maximizable, false); // Развернуть на весь экран
	win.maximizeLoading = safeAssign(args.maximizeLoading, false); // Разварачивать на весь экран при загрузке
	win.minimizable = safeAssign(args.minimizable, true);  // Свернуть окно в пенель снизу
	win.collapsable = safeAssign(args.collapsable, true);  // Свернуть окно
	win.resizable = safeAssign(args.resizable, true);  // Возможность изменять размер окна
    win.overflowHtmlHidden = safeAssign(args.overflowHtmlHidden, false);  // Возможность изменять размер окна

	// Если можно разварачивать, то на двайно клик вешаем разварачивание окна 
	if(win.maximizable == true)
		win.dblclick = 'maximize';
	else
		win.dblclick = false;

	// Init dialog
	// -----------
	this.widget = $('<div></div>');
	if (args.content != null) $(this.widget).append(args.content);

	$(this.widget).dontScrollParent();

	// Set size
	// --------
	var maxWinHeight = $(window).height() - 50;
	var maxWinWidth = $(window).width() - 50;
	$(this.widget).css('max-width', safeAssign(args.maxWidth, maxWinWidth));

	// Where to add
	// ------------
	var appendTo = "body";
	if ($('.admin-desktop').size() > 0) appendTo = ".admin-desktop";

	// Конструируем окн
	// -----------------
	$(this.widget).dialog({

		// Основные свойства
		// -----------------
		title : win.title,
		modal : win.modal,
		autoOpen : win.autoOpen,
		minHeight : false,
		dialogClass : 'flex-window',
		appendTo: appendTo,
		resizable : win.resizable,

		// Геометрические размеры
		// ----------------------
		width : safeAssign(args.width, 'auto'),
		height : safeAssign(args.height, 'auto'),
		maxHeight: safeAssign(args.maxHeight, maxWinHeight),

        closeOnEscape: safeAssign(args.closeOnEscape, true),
		
		dialogClass: safeAssign(args.dialogClass, 'default-dialog'), 

		show : { effect: 'fade'},
		hide : { effect: 'fade'},

		// При закрытии, убираем обычное окно
		// ----------------------------------
		close : function(event, ui) {
			$('html').css('overflow', win.htmlOverflow);
			win.close(this);
		},

		// При открытии - центрирование
		// ----------------------------
		open : function() {
			win.htmlOverflow = $('html').css('overflow');
			win.realDialog = $(this).dialog('widget');
			win.reposition();
			win.calculatePosition();
			$(win.realDialog).bind('DOMNodeInserted', function(e) {
				if(e.eventPhase == 3) win.reposition();
			});

            if (win.overflowHtmlHidden == true) {
                $('html').css('overflow', 'hidden');
            }
		},

		// Для перерасчета
		// ---------------
		resizeStop : function() {
			win.calculateSize();
			win.calculatePosition();
			win.size = {
				'width' : $(this.realDialog).width(),
				'height' : $(this.realDialog).height()
			};
			win.oldSize = {
				'width' : $(this.realDialog).width(),
				'height' : $(this.realDialog).height()
			};
		},
		dragStop: function( event, ui ) {
			win.calculatePosition();
		},

		// Удаление
		// --------
		beforeClose: function() { delete win.widget;},
		position: 'center',

		focus: function() {
			var realDialog = $(this).dialog('widget');
			$('.flex-window').removeClass('active');
			$(realDialog).addClass('active');
		},

		dragStart: function() {
			var realDialog = $(this).dialog('widget');
			$('.flex-window').removeClass('active');
			$(realDialog).addClass('active');
		}

	}).dialogExtend({
			closable : true,
			collapsable: win.collapsable,
			maximizable: win.maximizable,
			minimizable: win.minimizable,
			dblclick : win.dblclick,

			load : function(evt, dlg){
				if(win.maximizeLoading)
					$(this).dialogExtend('maximize');
			},
			beforeCollapse : function(evt, dlg){
				$('html').css('overflow', win.htmlOverflow);
			},
			beforeMaximize : function(evt, dlg){
				win.htmlOverflow = $('html').css('overflow');
				$('html').css('overflow', 'hidden');
			},
			beforeMinimize : function(evt, dlg){
				$('html').css('overflow', win.htmlOverflow);
				$(win.windowToolbar.widget).hide();
			},
			beforeRestore : function(evt, dlg){
				$('html').css('overflow', win.htmlOverflow);
				$(win.windowToolbar.widget).show();
			},
		});

	$(this.realDialog).click(function(){
		$(this).addClass('active');
	});

	// Если есть иконка, устанавливаем
	// -------------------------------
	if(win.icon) {
		$(this.widget).data( 'uiDialog' )._title = function(title) {
			title.prepend( this.options.title );
		};

		var icon = $('<span class="flex-window-icon" />').css({'backgroundImage' : 'url('+win.icon+')'});
		$(this.widget).dialog('option', 'title', $(icon));
	}

	this.calculateSize();

	// Создаем панель
	// --------------
//	this.realDialog = this.widget.dialog("widget");
	this.windowToolbar = new Flex.WindowToolbar();
	$(this.realDialog).find(".ui-dialog-titlebar").after(this.windowToolbar.widget);

	// Если нужно, присваиваем класс
	// ----------------------------
	if (args.class !== null) {

		// Для массива
		// -----------
		if (typeof(args.class) == 'object') {
			$.each(args.class, function(classId, classTitle) {
				$(win.realDialog).addClass(classTitle);
			});
		}
		// Для строки
		// ----------
		else if (typeof(args.class == 'string')) {
			$(win.realDialog).addClass(args.class);
		}
	}

}


// Наследование объекта
// --------------------
Flex.Window.prototype = $.extend({}, Events.prototype, {


	// Центриование окна
	// -----------------------
	reposition: function() {

		// Get current size
		// ----------------
		this.size = {
			'width' : $(this.realDialog).width(),
			'height' : $(this.realDialog).height()
		}

		// Если точка уже существует, то производим смещение
		if(this.point != null && this.size.height != 0 && this.point.left != 0 && this.point.top != 0 && this.oldSize.width != null && this.oldSize.height) {

			// Not larger than 0
			// ------------------
			var offsetTop = this.point.top + ((this.oldSize.height - this.size.height) / 2);
			if (offsetTop < 0) offsetTop = 0;

			var offsetLeft = this.point.left + ((this.oldSize.width - this.size.width) / 2);
			if (offsetLeft < 0) offsetLeft = 0;

			$(this.realDialog).css({ top: offsetTop, left: offsetLeft });
		}

		this.calculatePosition();

		// Old size
		// --------
		this.oldSize = {
			'width' : $(this.realDialog).width(),
			'height' : $(this.realDialog).height()
		}

	},

	calculatePosition : function() {
		this.point = {
			left : $(this.realDialog).position().left,
			top  : $(this.realDialog).position().top
		}
	},

	// Вычисление размеров окна
	// ------------------------
	calculateSize : function() {
		var width = $(this.widget).parent().width();
		var mode = 'small';

		if (width > 1900) mode =  'xlarge';
		else if (width > 1280) mode = 'large';
		else if (width > 800) mode = 'normal';
		else if (width > 500) mode = 'medium';

		$(this.widget).parent().attr('data-flex-width', mode);

	},

	// Set window title
	// ----------------
	setTitle: function(title) {
		$(this.realDialog).find('.ui-dialog-title').html(title);
	},

	// Close window
	// ------------
	close : function(dialog) {
		$(this.widget).dialog('destroy').remove();
		$(this.realDialog).remove();
	}

});
