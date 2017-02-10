PrinterApp.controllersModule.controller("PrintPreviewerCtrl", [ '$scope', '$timeout', '$q', 'EndpointRequest', 'OptionsService', 
											function($scope, $timeout, $q, EndpointRequest, options) {
	var vm = this, initializing = true, canceller = $q.defer();
	vm.options = options;
	vm.fetchingPreviewLink = false;

	$scope.$watch(angular.bind(this, function(){
		return vm.options.pagerange;
	}), function(newPagerange) {
		if(initializing) {				//prevent initial execution
			$timeout(function() { initializing = false; });
			return;
		}

		if(!newPagerange) return; 		//bad pagerange, don't make request

		canceller.resolve('Cancelling previous preview link'); //resolve previous request to avoid multiple request
		canceller = $q.defer();

		vm.fetchingPreviewLink = true;	//before send turn on loader

		var data = {
			'pageRange': newPagerange,
			'layout': vm.options.layout		
		};

		//fetch new preview link
		EndpointRequest.fetchPreviewLink(
			data, 
			canceller.promise
		).then(function(resp){
					vm.options.previewLink = resp.data;
				}, function(resp) {
					if(resp.status == -1) return; 	//request aborted
					console.log('Failed to get preview link');
			}).then(function() {
				vm.fetchingPreviewLink = false;	//turn off loader
		});
	});
}]);	