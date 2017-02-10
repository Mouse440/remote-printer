PrinterApp.directivesModule.directive('printPreviewer', function() {

	var template = '<embed class="preview-plugin hidden-xs" type="application/pdf"></embed>'

	return {
		link: function(scope, elm, attrs) {
			var parent = elm.find('#preview-pdf');
			scope.$watch(attrs['previewLink'], function(newPreviewLink) {
				parent.empty();
				var newPreview = angular.element(template);
				parent.append( newPreview.prop('src', newPreviewLink) );
			});
		}	
	}
});