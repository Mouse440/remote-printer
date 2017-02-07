PrinterApp.controllersModule.controller('AppCtrl', ['$scope', 'ModalService', 'EndpointRequest' , function($scope, ModalService, EndpointRequest) {

	var vm = this;

	vm.filename = '';
	vm.allowance = '';
	vm.previewLink = '';
	vm.page_amount = '';

	vm.clearFile = function() {
		vm.filename = '';
		vm.allowance = '';
		vm.previewLink = '';
		vm.page_amount = '';

		EndpointRequest.clearFile();
	}

	vm.showPrintOptions = function() {
		ModalService.showModal({
			templateUrl: 'app/components/print_options_form/print_options_form.html',
			controller: 'PrintOptionsCtrl',
			controllerAs: 'optionsCtrl',
			inputs: { 
				data:{
					allowance: vm.allowance,
					previewLink: vm.previewLink,
					page_amount: vm.page_amount,
					print_job_done: false
				}
			}
		}).then(function(modal){
			modal.element.modal();
		    modal.close.then(function(result) {
		    	vm.clearFile();
		    });
		});
	}

	$scope.$on('uploadDataFetched', function(e, resp){ 
		vm.filename = resp.config.data.file.name;
		vm.allowance = resp.data.allowance;
		vm.previewLink = resp.data.previewLinks;
		vm.page_amount = resp.data.amount;
	});

	$scope.$on('uploadDataRemove', vm.clearFile);
}]);