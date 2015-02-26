// Поле с зависимостью
// ======================================================================
UI.FormInputs.dependent = function(args) {

  UI.FormInput.call(this, args);
	
	// Создаем элемент
	// -----------------------------
	this.widget = $('<div class="inputDependent"></div>');
	var inputDependent = this.widget;

	if(args.format.format != null) {			
		var dependentForm = new UI.Form({'type' : 'form', 'id' : 'dependent'+(Math.random())*1000, 'format' : args.format.format, 'object' : args.value});
		this.dependentForm = dependentForm;
	}

	var ifSelected = $('<input type="checkbox" name="ifSelected" />').change(function(){
		if($(this).is(':checked')) {
	  	$(dependentForm.form).show();
		} else {
		  $(dependentForm.form).hide();
		}
	});
		                
	$(ifSelected).appendTo(this.widget);  
	$(inputDependent).append(dependentForm.form);	
	
	if (args.value != null && args.value != false) { 
		$(ifSelected).prop("checked", "checked");
		$(this.widget).append(this.dependentForm.form);
	} else {
		$(dependentForm.form).hide();
	}

}

UI.FormInputs.dependent.prototype = $.extend({}, UI.FormInput.prototype, {

	// Чтение данных
	// -----------------------
	getValue : function() {
		if($(this.widget).children('input[name="ifSelected"]').prop("checked")) {
			// Забираем данные
			// ----------------
			var formData = this.dependentForm.getValue();
		  value = formData;
		} else 
			value = false;
			
		return value;
	}
});