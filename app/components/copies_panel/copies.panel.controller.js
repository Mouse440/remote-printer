PrinterApp.controllersModule.controller('CopiesPanelCtrl', ['$scope','OptionsService', function($scope, options){ 
	var vm = this;

	this.options = options;

	$scope.regex = /^\d+$/;
	$scope.increment = function(val) {
		val = val || 1; 

		var newCopiesVal = parseInt(options.copies) + val;
		
		if(newCopiesVal <= 999 && newCopiesVal > 0) {
			options.copies = newCopiesVal;
		}
	}
}]);