// var services = angular.module('appServices',[]);

PrinterApp.servicesModule.service('EndpointRequest', ['$http', 'Endpoints', 'Upload', function($http, Endpoints, Upload) {
	
	this.upload = function(data) {
			var options = {
                url: Endpoints.upload,
                data: data
            }
			return Upload.upload(options);
		};
	this.fetchPreviewLink = function(data, promise) {
			return $http({
				url: Endpoints.fetchPreviewLink,
				params: data,
				method: 'GET',
				timeout: promise
			});
		};
	this.executePrint = function(data) {
			return $http({
				url: Endpoints.executePrint,
				params: data,
				method: 'GET'
			});
		},
	this.clearFile = function() {
			return $http.delete(Endpoints.clearTransaction);
		};
}]);