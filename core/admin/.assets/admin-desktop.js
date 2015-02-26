// Объект на рабочем столе
// -----------------------
DesktopObject = function(args) {

		var desktopObject = this;

		// Create widget
		// -------------
		this.widget = $('<div class="admin-desktop-object"></div>');
		$(this.widget).addClass('object-' + args.class);

		// This is desktop object
		// ----------------------
		$(this.widget).data('desktop-object', true);

		// Add controller
		// --------------
		new ObjectController({ 'permanent' : true, 'widget' : this.widget, 'class' : args.class, 'id' : args.id});

		// Add menu
		// --------
		var menu = new Flex.ContextMenu({
			'parent' : this.widget,
			'groups' : [{
				'title' : 'Объект стола',
				'id' : 'desktop-object',
				'elements' : [
					{'title' : 'Убрать со стола', 'click' : function() {
						$(desktopObject.widget).remove();
					}}
				]
			}]

		});

}


// Перетаскивание
// --------------
$(function() {
	$(".admin-desktop").each(function() {

		var desktop = this;

		$(this).FlexDroppable({
			'contexts' : {
				'object' : function(object, ui, e) {


					// Если это уже созданный объект
					// -----------------------------
					if ($(ui).data('desktop-object') == true) {
						$(ui).css({'left' : e.clientX, 'top' : e.clientY});
					}

					// Иначе вставка на рабочий стол
					// -----------------------------
					else {
						var desktopObject = new DesktopObject({'class' : object.class, 'id' : object.id});
						$(desktopObject.widget).css({'left' : e.clientX, 'top' : e.clientY, 'position' : 'absolute'});
						$(desktop).append(desktopObject.widget);
					}

				}
			}
		});
	});
});
