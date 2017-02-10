describe('pagerange.controller', function() {
	var $controller, $scope, pagerangeCtrl;
	var defaultVal = 'default';

	// console.log(module('remotePrinter'));
	beforeEach(angular.mock.module('appControllers','appServices','appValues', 'ngFileUpload'));

	beforeEach(inject(function(_$controller_, _$rootScope_){
		$controller = _$controller_;
		$scope = _$rootScope_.$new();
	}));

	beforeEach(function() {
		pagerangeCtrl = $controller('PagerangeCtrl', {$scope: $scope} );
	});

	describe('$scope.checkRadioAll', function() {
		it('should alternate between radioAllSelected and radioOtherSelected', function() {

			$scope.checkRadioAll();
			// expect($scope.pagerange).toEqual(defaultVal); use spy
			expect($scope.radioAllSelected).toEqual(true);
			expect($scope.radioOtherSelected).toEqual(false);

			$scope.checkRadioOther();
			expect($scope.radioAllSelected).toEqual(false);
			expect($scope.radioOtherSelected).toEqual(true);
		});
	});
});