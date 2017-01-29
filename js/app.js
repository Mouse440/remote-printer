var app =  angular.module('remotePrinter', ['ngFileUpload','angularModalService']);

app.value('Endpoints', {
	upload: 'php/phase1/validateAndFetch.php'
});

app.filter('pluralize', function(){
	return function(num) {
		var totalText = (num === 1 || num === 0) ? ' page' : ' pages';
		return num + totalText;
	}
});

app.directive('copiesPanel', function() {
	return {
		restrict: 'E',
		templateUrl: 'views/copies_panel.html'
	}
});

// app.service('Transaction', function() {
// 	this.file = '';
// 	this.allowance = '';
// 	this.previewLinks = '';
// 	this.page_amount = '';
// });
app.controller('AppCtrl', ['$scope', 'ModalService', function($scope, ModalService) {

	var vm = this;

	vm.filename = '';
	vm.allowance = '';
	vm.previewLink = '';
	vm.page_amount = '';

	vm.showPrintOptions = function() {
		ModalService.showModal({
			templateUrl: 'views/print_options_form.html',
			controller: 'PrintOptionsCtrl',
			controllerAs: 'optionsCtrl',
			inputs: { 
				data:{
					allowance: vm.allowance,
					previewLink: vm.previewLink,
					page_amount: vm.page_amount
				}
			}
		}).then(function(modal){
			modal.element.modal();
		    modal.close.then(function(result) {
		        console.log("modal closed:");
		    });
		});
	}

	$scope.$on('uploadDataFetched', function(e, resp){ 
		vm.filename = resp.config.data.file.name;
		vm.allowance = resp.data.allowance;
		vm.previewLink = resp.data.previewLinks;
		vm.page_amount = resp.data.amount;

		console.log("uploadDataFetched called", vm);
	});

	$scope.$on('uploadDataRemove', function(){ 
		vm.filename = '';
		vm.allowance = '';
		vm.previewLink = '';
		vm.page_amount = '';

		console.log("uploadDataRemove called", vm);
	});


}]);

app.controller('CopiesPanelCtrl', ['$scope', function($scope){ 
	$scope.increment = function(val) {
		
		if(!val) val = 1;

		var copies = parseInt($scope.$parent.optionsCtrl.copies);
		$scope.$parent.optionsCtrl.copies = copies + val;
	}
}]);

app.controller('PrintOptionsCtrl', ['$scope', 'data', 'close', function($scope, data, close) {
	var vm = this;

	vm.allowance = data.allowance;
	vm.previewLink = data.previewLink;
	vm.page_amount = data.page_amount;

	vm.copies = 1;
	vm.pages = '';

	// TODO: create a directive for copies-panel

	

	console.log(data);
}]);

app.controller('UploadCtrl', ['$scope', 'Upload', '$timeout', 'Endpoints', 
										function ($scope, Upload, $timeout, Endpoints) {
	console.log(Endpoints);
	
	$scope.$watch(angular.bind(this, function(){
    	return this.file;
    }), function(newVal) {
       	upload(newVal);
    });

	var vm = this;
    vm.error = ''; 
    vm.progress = '';
    vm.progressPercentage = -1;

    var upload = function (file) {
        if (file) {
        	console.log(file);
        	vm.filename = '';				//reset name
            if (!file.$error) {
                Upload.upload({
                    url: Endpoints.upload,
                    data: {
                      file: file  
                    }
                }).then(function (resp) {		//success
                	console.log(resp);
                	vm.progressPercentage = -1;
                	vm.filename = resp.config.data.file.name;

                	$scope.$emit('uploadDataFetched', resp);

                    // $timeout(function() {
                    //     $scope.log = 'file: ' +
                    //     resp.config.data.file.name +
                    //     ', Response: ' + JSON.stringify(resp.data) +
                    //     '\n' + $scope.log;
                    // });
                }, function(resp) {		//error handling
					if (response.status > 0)
					        vm.error = response.status + ': ' + response.data;
                	
                }, function (evt) {
                    var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);

                    vm.progressPercentage = progressPercentage;
                    vm.progress = vm.progressPercentage + '%';
                });
            } 
        }
    };

    vm.clearFile = function() {
    	vm.filename = '';
    	$scope.$emit('uploadDataRemove');
    }
}]);