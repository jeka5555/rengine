UI.FormInputs.file = function(args) {

	this.folderID = safeAssign(args.format.folderID, null);
	this.folderPath = safeAssign(args.format.folderPath, null);

	// Create input
	// ------------
	var fileInput = this;
    UI.FormInput.call(this, args);

	// Widget
	// ------
	this.widget = $('<div> \
		<div class="preview"></div> \
		<div class="controls"></div> \
	</div>');

	// Add uploader
	// ------------
	this.uploader = new UI.FileUploader({
		'multiple' : false,
		'showPreview' : false,
		'folderID' : this.folderID,
		"folderPath" : this.folderPath
	});

	// Append uploader
	// ---------------
	$(this.uploader.widget).appendTo($(this.widget).children(".controls"));
	this.uploader.addListener('fileUploadComplete', function(e) {
		mediaInput.setValue(e.data);
	});

	// Если есть значение, обновляем
	// ------------------------------
	if (args.value != undefined && args.value != null) this.updatePreview();

};

UI.FormInputs.file.prototype = $.extend({}, UI.FormInput.prototype, {

	// Set value
	// ---------
	setValue : function(value) {
		this.value = value;
		this.updatePreview();
	},

	// Get value
	// ---------
	getValue : function() {
		return this.value;
	},

	// Update file preview
	// -------------------
	updatePreview : function() {

		var mediaInput = this;

		// Skip if file isn't set
		// ----------------------
		if (this.value == null) return false;

		// Clear preview
		// -------------
		$(this.widget).find(".preview").empty().append(preview);

		// Get file preview
		// ----------------
		var preview = API.action({
			'action' : '/module/media/previewFile',
			'data' : {'file' : this.value},
			'callback' : function(data) {
			}
		});
	}

});