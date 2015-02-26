// Flex - библиотека мобильных ui элементов
// -----------------------------
Flex = {};


// Droppable
// ----------------------------------
$.fn.FlexDroppable = function(args) {

	// Создаем объект с хранением свойств
	// -----------------------------
  var droppable = args;

	// Стандартный метод
	// -----------------------------
	$(this).droppable({
		greedy : safeAssign(args.greedy, true),
		tolerance : 'pointer',
		drop : function(e, ui) {
			var object = ui.draggable;

			// Если существуют контенксты объекта
			// --------------------
			if (droppable.contexts !== undefined && $(object).data('contexts') !== undefined)  {

				var objectContexts = $(object).data('contexts');
				$.each(droppable.contexts, function(contextID, contextFunction) {

					if (objectContexts[contextID] !== undefined) {
						contextFunction(objectContexts[contextID], object, e);
					}
				});
			}

			// Если есть общий метод, вызываем
			// ---------------------
			if (droppable.drop !== undefined) droppable.drop(
				$(object).data(),
				{
					'object' : object,
					'event' : e
				}
			);

		}
	});

}



// Draggable
// ----------------------------------
$.fn.FlexDraggable = function(args) {

	// Здесь храним свойства
	// ------------------------------
	var draggable = args;

	// ------------------------------
	if (args.contexts !== undefined) {
		$(this).data('contexts', args.contexts);
	}

	var object = this;

	// Стандартное оформление
	// ------------------------------
	$(this).draggable({
		'distance' : 8,
		'cursor' : 'pointer',
		'appendTo' : 'body',
		'drag' : function(event, ui) {
			// Использовать ctrl?
			// ------------------
			if (args.useCtrl == true) {
					if (!event.ctrlKey) return false;
			}
		},
		'helper' : function() { var helper = $(this).clone(); $(helper).addClass("dragObject"); return helper; },
		'zIndex' : 10000,
		'opacity' : 0.8
	});

}

