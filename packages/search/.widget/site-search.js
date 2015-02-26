$(function() {

	$(".widget-site-search").each(function() {

		var widget = this;


		// Create search function
		// -----------------------
		var searchFunction = function() {
			// Get value
			// ---------
			var value = $(widget).find(".search").val();

			// Submit request
			// --------------
			API.action({
				'action' : '/module/search/search',
				'data' : value
			});
		};

		// Bind events
		// -----------
		$(this).find(".icon").click(searchFunction);
		$(this).find("input").change(searchFunction);
	});


});
