Component.register({

	// Create component
	// ----------------
	'type' : 'content-block',
	'id' : 'html',
	'title' : 'HTML-содержимое',
	'inherit' : [
		['component', 'content-block']
	],
	'constructor' : function(args) {

		// Inerit
		// ------
		this.parentConstructor.call(this, args);

	},

	// Edit mode
	// ---------
	'renderEdit' : function() {

		var block = this;

        this.editorID = 'text' + Math.round(Math.random() * 232030203023);

		// Add textarea
		// ------------
		this.textarea = $('<textarea></textarea>').attr("id", this.editorID).appendTo(this.contentWidget);

		// Set data
		// --------
		if (this.data != null) {
			$(this.textarea).val(this.data);
		}

		// Expand to full editor
		// --------------------

        block.tinymceInit = function() {
            tinymce.init({
                selector: '#'+block.editorID,
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
        }

        block.tinymceInitInterval = setInterval(function() {
            if ($('#'+block.editorID).length > 0) {
                block.tinymceInit();
                clearInterval(block.tinymceInitInterval);
            }
        }, 100);

	},


	// Value
	// -----
	'setValue' : function(value) {
		this.data = value;
		$(this.textarea).val(value);
	},

	// Get value
	// ---------
	'getValue' : function() {

		// Get data
		// --------
		this.data = tinymce.EditorManager.get(this.editorID).getContent();

		// Classic way
		// -----------
		return this.parent.getValue.call(this);
	}
});

