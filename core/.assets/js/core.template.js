CoreTemplate = function() {
}


CoreTemplate.appendToPlaceholder = function(tpl, placeholder, html) {

	var placeholder = $(tpl).find('*[data-tpl-placeholder=' + placeholder + ']');
	$(placeholder).append(html);
}

CoreTemplate.process = function(tpl, data) {
	var template = $(tpl);

	$(template).find("[tpl-source]").each(function() {


		// Get variable name
		// -----------------
		var varName = $(this).attr("tpl-source");
		if (varName == null) return;

		var s = 'data.'+varName;
		var pval = eval('data.'+varName);
		if (pval != null) {
			$(this).append(pval);
			$(this).attr("tpl-source", null);
		}

	});

	return template;
}