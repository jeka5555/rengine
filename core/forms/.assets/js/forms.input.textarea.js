// Textarea
// ========================================================================
UI.FormInputs.textarea = function(args) {

	var input = this;
	UI.FormInput.call(this, args);

    this.editorID = 'text' + Math.round(Math.random() * 232030203023);

	// Запоимнаем нстройки
	// --------------------
	this.toolbar = safeAssign(this.format.toolbar, 'tiny');

	// Создаем виджет
	// ------------------------
	this.widget = $('<textarea></textarea>').attr("id", this.editorID);
	this.initWidget();

	// Если значение задано, инициализируем его значением
	// ------------------------
	if (this.value != null) $(this.widget).val(this.value);

	// Для html добавляем редактор
	// ------------------------
	if (this.format.isHTML == true) {

  	var allowSource = safeAssign(this.format.allowSource, true);
		// Если редактор уже не добавлен, включаем
		// --------------------
		setTimeout(function() {

            tinymce.init({
                selector: '#'+input.editorID,
                script_url : '/core/.assets/js/tinymce/tinymce.min.js',
                language : 'ru',
                theme : "modern",
                plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern insertmedia"
                ],
                toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image insertmedia",
                toolbar2: "print preview media | forecolor backcolor emoticons",
						    table_cell_class_list: [
						        {title: 'Нет', value: ''},
						        {title: 'Ячейка с отступом', value: 'cell-padding'}
						    ]
            });

		}, 500);
	}

};

UI.FormInputs.textarea.prototype = $.extend({}, UI.FormInput.prototype, {


	// Set input value
	// ---------------
	setValue : function(value) {
		this.value = value;
		$(this.widget).val(value);
	},

	// Get value
	// ---------
	getValue : function() {
		var input = this;	
		if (tinymce.EditorManager.get(input.editorID) != null)		
			return tinymce.EditorManager.get(input.editorID).getContent();
		else
			return $(this.widget).val()			
	},

	// Get preview value
	// -----------------
	getPreviewValue: function() {
		var value = this.getValue();

		if (value == null) return null;
		if ($.trim(value) == '') return null;

		value = value.replace(/(<([^>]+)>)/ig,"");
		if (value.length > 50) value = value.substr(0, 50);

		return value;
	},

});
