PrinterApp.controllersModule.controller('PrintOptionsCtrl', ['$scope', '$q', '$timeout', 'data', 'EndpointRequest', 'OptionsService', 'close',  
										function($scope, $q, $timeout, data, EndpointRequest, options, close) {
	var vm = this;
	
	vm.options = options;

	vm.options.printJobDone = data.print_job_done; 			// print job is done, including error job result

	vm.options.originalPagerange = (parseInt(vm.options.page_amount) > 1) ? '1-' + vm.options.page_amount : '1';
	vm.options.pagerange = vm.options.originalPagerange;
	vm.options.pagerangeAmount = options.getRangeAmount(vm.options.pagerange, vm.options.page_amount);

	vm.closeModal = function() {
		close({}, 500);	
	}
}]);