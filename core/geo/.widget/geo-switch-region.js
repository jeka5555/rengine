$(function() {

	// Assign a code
	// -------------
  $(".widget-geo-switch-region").each(function() {

		var button = this;

		$(this).click(function() {
			API.request({
				'uri' : '/module/geo/showRegionSelector'
			});
		});
	});
	
});