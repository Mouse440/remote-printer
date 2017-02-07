PrinterApp.controllersModule.controller('CopiesPanelCtrl', ['$scope', function($scope){ 
	$scope.regex = /^\d+$/;
	$scope.increment = function(val) {
		val = val || 1; 

		var newCopiesVal = parseInt($scope.$parent.optionsCtrl.copies) + val;
		
		if(newCopiesVal <= 999 && newCopiesVal > 0) {
			$scope.$parent.optionsCtrl.copies = newCopiesVal;
		}
	}
}]);