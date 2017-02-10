PrinterApp.controllersModule.controller('AppCtrl', ['$scope', 'ModalService' , 'OptionsService', 
												function($scope, ModalService, options) {

	var vm = this;	

	vm.options = options;

	vm.showPrintOptions = function() {
		ModalService.showModal({
			templateUrl: 'app/components/print_options_form/print_options_form.html',
			controller: 'PrintOptionsCtrl',
			controllerAs: 'optionsCtrl',
			inputs: { 
				data:{
					print_job_done: false
				}
			}
		}).then(function(modal){
			modal.element.modal();
		    modal.close.then(function(result) {
		    	vm.options.clearTransaction();
		    });
		});
	}
}]);