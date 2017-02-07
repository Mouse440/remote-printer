PrinterApp.controllersModule.controller('UploadCtrl', ['$scope', '$http', 'EndpointRequest', '$timeout', 
										function ($scope, $http, EndpointRequest, $timeout) {
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
            if (!file.$error) {
            	EndpointRequest.upload({
            		file: file
            	}).then(function (resp) {		//success
                	vm.progressPercentage = -1;
                	$scope.$emit('uploadDataFetched', resp);
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
    	$scope.$emit('uploadDataRemove');
    }
}]);