PrinterApp.controllersModule.controller('PrintOptionsCtrl', ['$scope', '$http', '$q', '$timeout', 'data', 'EndpointRequest', 'OptionsUtilities', 'close',  
										function($scope, $http, $q, $timeout, data, EndpointRequest, OptionsUtilities, close) {
	var vm = this, initializing = true, canceller = $q.defer();
	$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	
	vm.allowance = data.allowance;
	vm.previewLink = data.previewLink;
	vm.page_amount = data.page_amount;
	vm.printJobDone = data.print_job_done; 			// print job is done, including error job result

	vm.fetchingPreviewLink = false;
	vm.printInProgress = false;
	vm.printJobError = '';

	vm.copies = 1;
	vm.pagerange = (parseInt(data.page_amount) > 1) ? '1-' + data.page_amount : '1';
	vm.pagerangeAmount = OptionsUtilities.getRangeAmount(vm.pagerange, vm.page_amount);

	vm.total = function() {
		var total = vm.copies * vm.pagerangeAmount || 0;
		return total;
	}
	vm.closeModal = function() {
		close(null, 500);	
	}

	vm.layout = 'portrait'; // temporary default value 
	vm.twoSided = true;	 	// temporary default value 

	vm.executePrint = function() {

		var data = {
			range: vm.pagerange,
			copies: vm.copies,
			twoSided: vm.twoSided,  
			layout: vm.layout,
			total: vm.total()
		};

		vm.printInProgress = true;

		EndpointRequest.executePrint(
			data
		).then(function(resp){
				if(resp.data.status == 7) {
					vm.printJobDone = true;
					vm.allowance = resp.data.pagesLeft;
				} else if (resp.data.error) {
					vm.printJobError = resp.data.error;
				}
			}, function(resp){
				vm.printJobError = resp.data;
		}).then(function(data) {
			vm.printInProgress = false;
			vm.printJobDone = true;
		});
	}

	$scope.$watch(angular.bind(this, function(){
		return vm.pagerange;
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
			'layout': vm.layout		
		};

		//fetch new preview link
		EndpointRequest.fetchPreviewLink(
			data, 
			canceller.promise
		).then(function(resp){
					vm.previewLink = resp.data;
				}, function(resp) {
					if(resp.status == -1) return; 	//request aborted
					console.log('Failed to get preview link');
			}).then(function() {
				vm.fetchingPreviewLink = false;	//turn off loader
		});
	});
}]);