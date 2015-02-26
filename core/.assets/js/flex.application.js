// Базовое приложение
// ------------------
Application = function(args) {
	Events.call(this);
}

// ----------------------------
Application.prototype = $.extend({}, Events.prototype, {

	// Информация о приложении
	// -----------------------
	appID : 'dummy',
	title : 'Приложение',

	// Завершение работы
	// -----------------
	close: function()  {
		if (this.window != null) this.window.close();
	},

	// Вызов метода
	// ------------
	command: function(method, args, callback) {

		// Определяем наличие метода на стороне клиента
		// --------------------------------------------
		if (this.hasOwnProperty('command' + method)) this['command' + method].call(this, args, callback);
		else API.action({'action' : 'apps/' + this.appID + '/' + method, 'data' : args, 'callback' : callback });
	}

});
