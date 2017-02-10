describe('pagerange.input.directive', function() {
	var $scope, $compile, $rootScope, form;

	beforeEach(angular.mock.module('appDirectives', 'appValues'));

	beforeEach(inject(function(_$compile_, _$rootScope_) {
		$compile = _$compile_;
		$rootScope = _$rootScope_;

		$scope = $rootScope;
		$scope.pageAmount = 5;

		var element = $compile('<form name="optionsForm">' +
				'<input ng-model="pagerange" page-amount="{{pageAmount}}" name="pageRange" pagerange-input />' + 
			'</form>' 
			)($scope);
		
		$rootScope.$digest();

		form = $scope.optionsForm;
	}));

	it('should be valid when empty', function() {
		form.pageRange.$setViewValue('');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should be valid when numbers are in ascending order', function() {
		form.pageRange.$setViewValue('1,2,5');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should be valid when following this sequence number,number-number', function() {
		form.pageRange.$setViewValue('1,2-3');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should be valid when following this sequence number-number,number', function() {
		form.pageRange.$setViewValue('1-2,3');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should be valid when following this sequence number-number,number-number', function() {
		form.pageRange.$setViewValue('1-2,3-4');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should be valid when following this sequence number-number,number,number', function() {
		form.pageRange.$setViewValue('1-2,3,4');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should be valid when following this sequence number-number,number,number-numer', function() {
		form.pageRange.$setViewValue('1-2,3,4-5');
		expect(form.pageRange.$valid).toEqual(true);
	});
	it('should not have a last number larger than pageAmount', function() {
		form.pageRange.$setViewValue('1-7');
		expect(form.pageRange.$invalid).toEqual(true);
	});
	it('should not have duplicated number', function() {
		form.pageRange.$setViewValue('1-1');
		expect(form.pageRange.$invalid).toEqual(true);
	});
	it('should not have a negative number', function() {
		form.pageRange.$setViewValue('-7');
		expect(form.pageRange.$invalid).toEqual(true);
	});
	it('should not have descending number sequence', function() {
		form.pageRange.$setViewValue('3,2,1');
		expect(form.pageRange.$invalid).toEqual(true);
	});
	it('should not have a number larger than pageAmount in single number case', function() {
		form.pageRange.$setViewValue('7');
		expect(form.pageRange.$invalid).toEqual(true);
	});
	it('should not have any characters [a-zA-Z]', function() {
		form.pageRange.$setViewValue('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
		expect(form.pageRange.$invalid).toEqual(true);
	});
	it('should not follow this sequence number-number-number', function() {
		form.pageRange.$setViewValue('1-2-4');
		expect(form.pageRange.$invalid).toEqual(true);
	});
});