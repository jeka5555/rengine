Component.register({

	// Create component
	// ----------------
	'type' : 'input',
	'id' : 'contentEditor',
	'title' : 'Расширенный редактор',

	// Constructor
	// -----------
	'constructor' : function(args) {

		// This
		// ----
		var editor = this;

		// Create widget
		// -------------
		this.widget = $('<div class="content-editor">' +
			'<div class="content-editor-toolbar"></div>' +
			'<div class="content-editor-content"></div>' +
		'</div>');

		// Subwidgets
		// ----------
		this.toolbarWidget = $(this.widget).children('.content-editor-toolbar');
		this.contentWidget = $(this.widget).children('.content-editor-content');

		// Options
		// -------
		this.blockButtons = [];

		// Sortable
		// --------
		$(this.contentWidget).sortable({
			handle : ".drag-handle",
			cursor : "move"
		});

		// Refresh on sort
		// ---------------
		$(this.contentWidget).on("sortstop", function(e, ui) {
			editor.refreshOrder();
		});

		// Update
		// ------
		this.update(args);


	},

	// Position
	// --------
	'refreshOrder': function(args) {

		var blocks = [];

		$(this.contentWidget).children(".content-editor-block").each(function() {

			var block = $(this).data('contentBlock');
			if (block == null) return;

			blocks.push(block);
		});

		this.blocks = blocks;
	},

	// Update
	// ------
	'update' : function(args) {

		var editor = this;
		if (args == null) args = {};

		// Options
		// -------
		this.content = [];
		if (args.content != null) this.content = args.content;

		// Blocks are here
		// ---------------
		this.blocks = [];
		this.value = [];


		// Set mode
		// --------
		this.mode = safeAssign(args.mode, 'edit');
		this.setMode(this.mode);

		// Update value
		// ------------
		if (args.value != null) this.value = args.value;

		if (this.value != null && (this.value instanceof Array)) {
			$.each(this.value, function(blockIndex, block) {
				editor.appendBlock(block);
			});
		}

		// Update widgets
		// --------------
		this.updateWidget();

	},


	// Update widget content
	// ----------------------
	'updateWidget' : function() {
		this.updateToolbar();
	},


	// Update toolbar
	// --------------
	'updateToolbar' : function() {

		// Editor
		// ------
		var editor = this;

		// Add block types button
		// ----------------------
		blockComponents = Components.getByType('content-block');

		$.each(blockComponents, function(blockIndex, blockData) {

			// Button
			// ------
			var blockButton = new FlexButton({
				'title' : blockData.title,
				'mode' : 'icon',
				'click' : function() {

					// Append new block
					// ----------------
					editor.appendBlock({'type' : blockIndex});
				}
			});

			// Add button to list
			// ------------------
			editor.blockButtons.push(blockButton);

			// Add classes
			// -----------
			$(blockButton.widget).attr('data-block-type', blockIndex);

			// Add button to toolbar
			// ---------------------
			$(editor.toolbarWidget).append(blockButton.widget);
		});

		// Add divider
		// -----------
		$(editor.toolbarWidget).append('<div class="divider"/>');

		// Mode button
		// -----------
		this.modeButton = new FlexButton({
			'title' : 'Режим',
			'mode' : 'icon',
			'id' : 'toggle-mode',
			'click' : function() {
				editor.toggleMode();
			}
		});
		$(editor.toolbarWidget).append(this.modeButton.widget);

		// Clear button
		// ------------
		this.clearButton = new FlexButton({
			'id' : 'trash',
			'title' : 'Очистка',
			'mode' : 'icon',
			'click' : function() {
				editor.clear();
			}
		});
		$(editor.toolbarWidget).append(this.clearButton.widget);

	},

	// Append single block
	// -------------------
	'appendBlock' : function(args) {

		var editor = this;

		// Get component
		// -------------
		var blockComponent = Components.get('content-block', args.type);
		if (blockComponent == null) return;

		// Create block
		// ------------
		var block = blockComponent.getInstance(args);
		// Set type
		// --------
		block.type = args.type;

		// Up block
		// --------
		block.addListener('up', function() {

			// Find block
			// ----------
			var blockPosition = editor.blocks.indexOf(block);

			// Don't up toppest
			// ----------------
			if (blockPosition == 0) return;

			var topBlock = editor.blocks[blockPosition-1];

			// Move widget
			// -----------
			$(block.widget).insertBefore(topBlock.widget);

			// Swap
			// ----
			editor.blocks[blockPosition] = topBlock;
			editor.blocks[blockPosition-1] = block;

		});

		// Down block
		// ----------
		block.addListener('down', function() {

			// Find block
			// ----------
			var blockPosition = editor.blocks.indexOf(block);

			// Don't up toppest
			// ----------------
			if (blockPosition == editor.blocks.length - 1) return;

			var bottomBlock = editor.blocks[blockPosition+1];

			// Move widget
			// -----------
			$(block.widget).insertAfter(bottomBlock.widget);

			// Swap
			// ----
			editor.blocks[blockPosition] = bottomBlock;
			editor.blocks[blockPosition+1] = block;

		});

		// Delete single block
		//  -----------------
		block.addListener('delete', function() {

			var blockPosition = editor.blocks.indexOf(block);
			if (blockPosition != -1) {
				$(editor.blocks[blockPosition].widget).remove();
				editor.blocks.splice(blockPosition, 1);
			}

		});


		// Append to blocks
		// ----------------
		this.blocks.push(block);

		$(this.contentWidget).append(block.widget);
		$(block.widget).focus();
	},

	// Update content widget
	// ---------------------
	'updateContent' : function() {

		var editor = this;

		// For edit mode, recreate all blocks
		// ----------------------------------
		if (this.mode == 'edit') {

			// Clear content widget
			// --------------------
			$(editor.contentWidget).empty();

			// Append blocks
			// -------------
			$.each(this.blocks, function(blockIndex, block) {
				$(editor.contentWidget).append(block.widget);
			});
		}

		// For preview mode, render preview
		// -------------------------------
		else {

			// Detach blocks
			// -------------
			$.each(this.blocks, function(blockIndex, block) {
				$(block.widget).detach();
			});

			// Clear content widget
			// --------------------
			$(editor.contentWidget).empty();

			// Request whole render
			// --------------------
			API.action({
				'action' : '/module/content/renderContent',
				'data' : this.getValue(),
				'callback' : function(result) {

					if (result != null) {
						$(editor.contentWidget).append(result);
					}
				}
			});

		}




	},

	// Set new mode
	// ------------
	'setMode' : function(mode) {

		// Don't set if mode already set
		// -----------------------------
		if (this.mode == mode) return;
		this.mode = mode;


		// Preview
		// -------
		if (this.mode == 'preview') {
			$.each(this.blockButtons, function(buttonIndex, button) {
				button.disable();
			});
		}

		// Edit
		// ----
		else {
			$.each(this.blockButtons, function(buttonIndex, button) {
				button.enable();
			});
		}

		this.updateContent();

	},

	// Clear editor
	// ------------
	'clear' : function() {
		$.each(this.blocks, function(blockPosition, block) {
			$(block.widget).remove();
		});

		this.blocks = [];
		this.value = [];
	},

	// Toggle mode
	// -----------
	'toggleMode' : function() {

		// Set mode
		// --------
		if (this.mode == 'edit') this.setMode('preview');
		else this.setMode('edit');

	},

	// Set new content
	// ---------------
	'setValue' : function(value) {
		this.value = value;
		this.updateContent();
	},

	// Get content
	// -----------
	'getValue' : function() {

		var value = [];

		$.each(this.blocks, function(blockIndex, block) {

			// Get block value
			// ---------------
			var blockValue = block.getValue();
			if (blockValue == null) return;

			// Or just append
			// --------------
			value.push(blockValue);

		});

		// Return value if we have something
		// ---------------------------------
		if (value.length > 0) return value;

	}
});

