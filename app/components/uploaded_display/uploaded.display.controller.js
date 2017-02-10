PrinterApp.controllersModule.controller('UploadedDisplayCtrl', ['$scope', 'OptionsService', 
																function($scope, options) {
	var vm = this;
	vm.options = options;

	vm.clearFile = options.clearTransaction;
}]);
