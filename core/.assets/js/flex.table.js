FlexTable = function(args) {


	// Init
	// ----
	Events.call(this);
	var table = this;

	// Structure data
	// --------------
	this.columns = args.columns;
	this.data = args.data;

	// Options
	// -------
	this.sort = args.sort;
	this.select = safeAssign(args.select, false);
	this.selectedRows = [];
	this.multiselect = safeAssign(args.multiselect, false);

	// Make heading
	// ------------
	this.widget = $('<div class="core-table"></div>');
	this.heading = $('<div class="core-table-heading"></div>').appendTo(this.widget);

	// Select cell
	// -----------
	if (table.select == true) {

		// Add select cell
		// ---------------
		if (table.select == true) {
			if (table.multiselect == true) this.columns.splice(0,0,{'id' : '@select', 'type' : 'checkbox', 'change' : function(cell, value) {
				if (value == true) table.selectRow(cell.row);
				else table.deselectRow(cell.row);
			}});
			else this.columns.splice(0,0,{'id' : '@select', 'type' : 'radio', 'change' : function(cell, value) {
				if (value == true) table.selectRow(cell.row);
				else table.deselectRow(cell.row);
			}});
		}

	}

	// Add data cells
	// --------------
	$.each(this.columns, function(columnIndex, column) {

		// Make a cell widget
		// ------------------
		var columnTitle = safeAssign(column.title, '');
		var cell = $('<div><span class="title">' + safeAssign(columnTitle, column.id) + '</span></div>');

		// Sort element append
		// -------------------
		if (column.sortable == true) {

			// Add class
			// ---------
			$(cell).addClass('sortable');

			// Mark sorted columns
			// -------------------
			var direction = 0;
			if (table.sort != null && table.sort.hasOwnProperty(column.id)) {

				// Direction
				// ---------
				direction = table.sort[column.id];
				if (direction == 1) $(cell).addClass('sorted-down');
				else $(cell).addClass('sorted-up');
			}

			// Add sort event
			// --------------
			$(cell).click(function() {
				direction = (direction == 1) ? -1 : 1;
				this.sort = {};
				this.sort[column.id] = direction;
				table.callEvent('sort', this.sort);
			});

		}

		// Append cell to this table
		// -------------------------
		$(cell).appendTo(table.heading);
	});


	// Collect all rows to table
	// -------------------------
	if (this.data != null && this.data.length > 0)
	$.each(this.data, function(rowIndex, rowData) {
		table.addRow({'data' : rowData, 'initialize' : args.rowInitialize, 'cellInitialize' : args.cellInitialize});
	});


}

// Flex table prototype
// --------------------
FlexTable.prototype = $.extend({}, Events.prototype, {


	// Select row
	// ----------
	selectRow : function(row) {
		$(row.widget).addClass('selected');
		var index = this.selectedRows.indexOf(row);
		if (index != -1 ) this.selectedRows.push(row);
	},

	deselectRow : function(row) {
		$(row.widget).removeClass('selected');
		var index = this.selectedRows.indexOf(row);
		if (index != -1 ) this.selectedRows.splice(index, 1);
	},

	// Generate single row
	// -------------------
	addRow : function(args) {

		var table = this;

		// Create table row
		// ----------------
		var row = new FlexTable.Row({
			'columns' : table.columns,
			'data' : args.data,
			'initialize' : args.initialize,
			'cellInitialize' : args.cellInitialize
		});

		// Append to table
		// ---------------
		$(row.widget).appendTo(table.widget);

	}

});

// Flex table cell
// ---------------
FlexTable.Cell = function(args) {

	// Init
	// ----
	Events.call(this);

	// Data
	// ----
	this.id = args.id;
	this.type = safeAssign(args.type, 'text');
	this.value = args.value;
	this.format = args.format;
	this.row = args.row;

	// Create widget
	// -------------
	this.widget = $('<div/>');

	// Add css wrapping
	// ----------------
	if (args.format != null && args.format.css != null) {
		$(this.widget).css(args.format.css);
	}

	// Update view
	// -----------
	this.update();

};

FlexTable.Cell.prototype = $.extend({}, Events.prototype, {

	// Update cell view
	// ----------------
	update : function() {

		var cell = this;

		// Add cell value
		// --------------
		switch (this.type) {

			// Image
			// -----
			case "image":

				var image = $('<img/>');
				if (this.value != null) $(image).attr('src', this.value);

				if (cell.format.width != null) $(image).css('width', cell.format.width);
				if (cell.format.height != null) $(image).css('width', cell.format.height);


				$(this.widget).append(image);
				break;

			// Checkbox
			// --------
			case "checkbox":

				if(cell.value === null) break;

				// Generate selector
				// -----------------
				var checkbox = $('<input type="checkbox" />');

				// Set initial value
				// -----------------
				if(cell.value === true) $(checkbox).attr('checked', true);

				// Bind change event
				// -----------------
				$(checkbox).bind('change', function() {

					// Bind change
					// ------------
					if(cell.format.change != null) {
						cell.format['change'](cell, $(checkbox).is(":checked"));
					}

					// True
					// ----
					if ($(checkbox).is(":checked")) {
						cell.callEvent('select');
						if(cell.format.select != null) {
							cell.format['select'](cell, true);
						}
					}

					// False
					// -----
					else {
						cell.callEvent('deselect');
						if(cell.format.deselect != null) cell.format['change'](cell, false);
					}
				});

				// Add selector cell
				// -----------------
				$(this.widget).append(checkbox);
				break;

			// Radio
			// --------
			case "radio":

				// Generate selector
				// -----------------
				var radio = $('<input type="radio" />');

				// Add id
				// -------
				$(radio).attr('name', this.id);

				// Change event
				// ------------
				$(radio).bind('change', function() {
					if ($(radio).is(":checked")) cell.callEvent('select');
					else cell.callEvent('deselect');
				});

				// Add selector cell
				// -----------------
				$(this.widget).append(radio);
				break;

			// Simple value
			// ------------
			default:
				$(this.widget).html(this.value);
				break;
		}

		// Add cell id
		// -----------
		if (this.id != null) $(this.widget).addClass('cell-id-' + this.id);
		if (this.type != null) $(this.widget).addClass('cell-type-' + this.type);

	}
});

// Flex table row
// --------------
FlexTable.Row = function(args) {

	// Init row
	// --------
	var row = this;
	Events.call(this);

	// Options
	// -------
	$.extend(this, args);

	// Create cells dict
	// -----------------
	this.cells = {};

	// Create widget
	// -------------
	this.widget = $('<div class="core-table-row"></div>');

	// Update row view
	// ---------------
	this.update();
}

// Table row prototype
// -------------------
FlexTable.Row.prototype = $.extend({}, Events.prototype, {

	initialize: function(args) {},

	click: function() {},

	// Update row view
	// ---------------
	update: function() {

		// Init
		// ----
		var row = this;
		$(row.widget).empty();

		// Add data cells
		// --------------
		$.each(this.columns, function(columnIndex, columnData) {

			// Detect cell type
			// ----------------
			var columnID = safeAssign(columnData.id, columnIndex);

			// Create cell
			// -----------
			var cell = new FlexTable.Cell({
				'id' : columnID,
				'row' : row,
				'type' : columnData.type,
				'format' : columnData,
				'value' : row.data[columnID]
			});

			// Add cell widget
			// ---------------
			$(row.widget).append(cell.widget);

			row.cells[columnID] = cell;

		});

		// Init row
		// --------
		this.initialize.call(this)
	},

	// Select row
	// ----------
	select: function() {

	},

	// Deselect row
	// ------------
	deselect: function() {

	},

	// Toggle selection
	// ----------------
	toggleSelection: function() {

	}


});
