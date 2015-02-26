UI.LoginPopup = function(args) {

	var toolbar = this;
	Events.call(this);
	this.widget = $(args.widget);

	// Находим кнопку входа и привязываем на нее вход
	// -------------------------
	$(this.widget).find(".user-login-button").click(function() {	
		API.action({'action' : '/module/users/requestLogin'});
	});

	// Находим кнопку выхода
	// -------------------------
	$(this.widget).find(".button-system-logout").click(function() {	
		API.action({'action' : '/module/users/logout'});
	});
}

UI.LoginPopup.prototype = $.extend({}, Events.prototype, {});

// Ждем нажатия
// --------------------------
$(window).keyup(function(e) {
	if (e.altKey == true && e.keyCode == 76) {	
		API.action({'action' : '/module/users/requestLoginForm'});
	}
});
