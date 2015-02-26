Events = function() {

	this.eventsList = [];
}

Events.init = function(object) {
	object.eventsList = [];
};

Events.prototype = {};


Events.prototype.addListener = function(event, callback ,args) {

	var events = this;

	if (typeof(event) == 'string') {
		this.eventsList.push({'event' : event, 'callback' : callback, 'args' : args });
	}

	else if (typeof(event) == 'object') {
		$.each(event, function(eventIndex, eventName) {
				events.eventsList.push({'event' : eventName, 'callback' : callback, 'args' : args });
		});
	}
};


// Функция вызова
// -------------------------
Events.prototype.callEvent = function(event, data) {				
	// Перебор всех событий
	// ---------------------
	for (eventID in this.eventsList) {
		var thisEvent = this.eventsList[eventID];
		if (thisEvent['event'] == event) {		
			var eventObject = {
				'event' : event,
				'data' : data,
				'context' : thisEvent['args']
			};
			thisEvent['callback'](eventObject);			
		}
	};
};

// Глобальный объект
// -------------------------
GlobalObject = function() {Events.init(this);};
GlobalObject.prototype = $.extend({}, Events.prototype);
var Global = new GlobalObject();