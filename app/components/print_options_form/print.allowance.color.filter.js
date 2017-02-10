PrinterApp.filtersModule.filter('printAllowanceColor', function() {
	return function(allowance) {
		var color = "orange";
		if(allowance > 0) {
			color = "green";
		}
		return color;
	}
});