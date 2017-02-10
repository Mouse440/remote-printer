PrinterApp.controllersModule.controller('PagerangeCtrl', ['$scope', 'OptionsService', function($scope, options) {

	var vm = this;

	vm.options = options;

	$scope.radioAllSelected = true;
	$scope.radioOtherSelected = false;

	$scope.checkRadioAll = function() {
		$scope.radioAllSelected = true;
		$scope.radioOtherSelected = false;
		vm.options.pagerange = vm.options.originalPagerange;
	}

	$scope.checkRadioOther = function() {
		$scope.radioAllSelected = false;
		$scope.radioOtherSelected = true;
	}

	$scope.updateRangeAmount = function(range) {
		vm.options.pagerangeAmount = options.getRangeAmount(range, vm.options.page_amount);
	}
}]); 

