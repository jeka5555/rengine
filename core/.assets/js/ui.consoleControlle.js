// Консоль разработчика
// ===========================================================================================
UI.ConsoleController = function(args) {

	var controller = this;
	this.widget = $("#developerConsole");

	// При клике на "закрывашку" - сворачиваем
	// ------------------------------
	$(this.widget).delegate(".consoleHeader",'click',function() {
		if (controller.isClosed != true) {
			$(controller.widget).animate({'height' : '20'});
			controller.isClosed = true;
		} else {
			$(controller.widget).animate({'height' : '50%'});
			controller.isClosed = false;
		}
	});

}

UI.ConsoleController.prototype = $.extend({}, Events.prototype);

