GeoConfirmCityController = function(args) {


	$(args.window.widget).find(".option.yes").click(function() {
		API.action({
			'action' : '/module/geo/confirmCity',
			'data' : {
				'cityID' : args.popup.data.cityID
			}
		});
		args.window.close();
	})

	$(args.window.widget).find(".option.no").click(function() {
		API.action({
			'action' : '/module/geo/rejectCity'
		});
		args.window.close();
	})

}
