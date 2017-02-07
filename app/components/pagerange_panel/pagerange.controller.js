PrinterApp.controllersModule.controller('PagerangeCtrl', ['$scope', 'OptionsUtilities', function($scope, OptionsUtilities) {

	var defaultPageRange = $scope.$parent.optionsCtrl.pagerange;

	$scope.radioAllSelected = true;
	$scope.radioOtherSelected = false;

	$scope.checkRadioAll = function() {
		$scope.radioAllSelected = true;
		$scope.radioOtherSelected = false;
		$scope.$parent.optionsCtrl.pagerange = defaultPageRange;
	}

	$scope.checkRadioOther = function() {
		$scope.radioAllSelected = false;
		$scope.radioOtherSelected = true;
	}

	$scope.getRangeAmount = OptionsUtilities.getRangeAmount;
}]);