Component.register({

	// Create component
	// ----------------
	'type' : 'content-block',
	'id' : 'media',
	'title' : 'Отображение медиа-файлов',
	'inherit' : [
		['component', 'content-block']
	],

	// Constructor
	// -----------
	'constructor' : function(args) {

		// Parent constructor
		// ------------------
		this.parentConstructor.call(this, args);

		// Data
		// ----
		this.data = {
			'mediaID' : null
		};
	},

	// Edit mode
	// ---------
	'renderEdit' : function() {
		this.mediaInput = new UI.FormInputs.media({'format' : {}});
		$(this.contentWidget).append(this.mediaInput.widget);
	},

		// Get value
	// ---------
	'getValue' : function() {

		// Get data
		// --------
		var media = this.mediaInput.getValue();
		if (media == null) return null;

		// Classic way
		// -----------
		this.data['mediaID'] = media;
		return this.parent.getValue.call(this);
	}


});

