// TriState
// ========================================================================
UI.FormInputs.triState = function(args) {
	UI.FormInput.call(this, args);

	this.widget = $('<div></div>');
	this.initWidget();

	var variantInput = $('<div class="triState-variant"> \
	<div><label><input type="radio" name="'+args.format.id+'[]" value="true" /> да</label></div> \
	<div><label><input type="radio" name="'+args.format.id+'[]" value="false" /> нет</label></div> \
	<div><label><input type="radio" name="'+args.format.id+'[]" value="undefined" /> не задано</label></div> \
	</div>');	
	
	$(variantInput).appendTo(this.widget);                                 
	$(this.widget).find('input[value="'+this.value+'"]').prop("checked", true);

};

UI.FormInputs.triState.prototype = $.extend({}, UI.FormInput.prototype, {

	// Чтение данных
	// -----------------------
	getValue : function() {
		var value = $(this.widget).find('input:checked').val();
		return eval(value);
	}
});
