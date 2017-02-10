PrinterApp.controllersModule.controller("ExecutePrintCtrl", ["$scope", "OptionsService", "EndpointRequest", 
										function($scope, options, EndpointRequest) {
	var vm = this;

	vm.executePrint = function() {

		var data = {
			range: options.pagerange,
			copies: options.copies,
			twoSided: options.twoSided,  
			layout: options.layout,
			total: options.total()
		};

		options.printInProgress = true;

		EndpointRequest.executePrint(
			data
		).then(function(resp){
				if(resp.data.status == 7) {
					options.printJobDone = true;
					options.allowance = resp.data.pagesLeft;
				} else if (resp.data.error) {
					options.printJobError = resp.data.error;
				}
			}, function(resp){
				options.printJobError = resp.data;
		}).then(function(data) {
			options.printInProgress = false;
			options.printJobDone = true;
		});
	}
}]);