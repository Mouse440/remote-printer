
PrinterApp.filtersModule.filter('pluralize', function(){
	return function(num) {
		var totalText = (num === 1 || num === 0) ? ' page' : ' pages';
		return num + totalText;
	}
});