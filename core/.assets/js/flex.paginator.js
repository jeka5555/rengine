Flex.Paginator = function(args) {

	Events.init(this);
	var paginator = this;

	// Аргументы
	// --------
	if (args == null) args = {};
	this.page = safeAssign(args.page, 0);
	this.count = safeAssign(args.count, 1);
	this.maxPages = safeAssign(args.maxPages, 6);

	// Если страниц меньше двух, то ничего не делаем
	// ---------------------------------------------
	if (this.count < 2) return;

	// Создаем виджет
	// --------------
	this.widget = $('<div class="flex-paginator"><span class="flex-paginator-title">Страницы:</span><span class="flex-paginator-pages"></span></div>');
	this.update();



}

Flex.Paginator.prototype = $.extend({}, Events.prototype, {


	// Заполнение
	// ----------
	update : function() {
		
		var paginator = this;
		$(this.widget).empty();

		// Определяем стартовую страницу и конечную
		// ----------------------------------------
		this.firstPage = 0;
		this.lastPage = 10;		


		// Первая страница
		// -----------------------------------------------
		var borrowed = 0;
		this.firstPage = this.page - Math.floor(this.maxPages / 2);
		if (this.firstPage < 0 ) {
			borrowed = 0 - this.firstPage;
			this.firstPage = 0;
		}


		this.lastPage = this.page + Math.floor(this.maxPages / 2) + borrowed;
		if (this.lastPage > this.count) this.lastPage = this.count;


		// Вставка первой
		// ---------------
		if (this.firstPage != 0) { var pageWidget = this.getPageButton(0, '&lt;&lt'); $(pageWidget).appendTo(this.widget); }

		// Вставка кнопок
		// --------------
		for (var page = this.firstPage; page <= this.lastPage - 1; page ++ ) {
			var pageWidget = this.getPageButton(page);
			$(pageWidget).appendTo(this.widget);
		}

		// Вставка последней страницы
		// --------------------------
		if (this.lastPage != this.count) { var pageWidget = this.getPageButton(this.count - 1, '&gt;&gt'); $(pageWidget).appendTo(this.widget); }		

		// Поле для прямой смены
		// ---------------------
		var numberInput = $('<input type="select" />').val(paginator.page + 1);
		$(numberInput).bind('change', function() {
			var value = Number($(numberInput).val());
			if (isNaN(value) || value == null || value < 1 || value > paginator.count) {
				alert('Неверное число, номер страницы должен быть в диапазоне от 1 до ' + paginator.count);
				$(numberInput).val(paginator.page + 1);
				return;
			}
			paginator.select(value - 1);
		});
		var numberWrap = $('<span class="flex-paginator-input"></span>').append(numberInput);
		$(this.widget).append(numberWrap);


	},


	// Кнопка на старницу
	// ------------------
	getPageButton : function(page, title) {

		var paginator = this;

		// Саоздем элементик страничной навигации
		// ------------------
		var pageWidget = $('<span class="flex-paginator-page"></span>')
			.attr('data-page', page)
			.attr('title', page + 1)
			.html(safeAssign(title, page + 1));

		// Если совпадает с текущей старницей, делаем активным
		// ---------------------------------------------------
		if (paginator.page == page) $(pageWidget).addClass('active');

		// Событие выбора страницы
		// -------------------
		$(pageWidget).click(function() {
			paginator.select(page);
		});

		return pageWidget;

		
	},
	

	// Выбор страницы
	// --------------
	select : function(page) {
		this.page = page;
		this.update();
		$(this.widget).find('.flex-paginator-page').removeClass('active');
		$(this.widget).find('.flex-paginator-page[data-page=' + this.page +']').addClass('active');
		this.callEvent('select', this.page);		
	}
});


