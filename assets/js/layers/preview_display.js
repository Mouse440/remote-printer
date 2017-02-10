"use strict"
var PreviewDisplay = (function(){

	var previewPlugin;
	var template;
	var parent;
	var loaderAnimation;
	var currentSrc = "default";
	/*
	* Initialize the preview
	* @param src - the source of preview file
	*/
	function loadPreview(src) {
		// var currentSrc = parent.children()[0].src;
		if(currentSrc.indexOf(src) == -1) {
			currentSrc = src;
			parent.empty();
			var newEle = $(template);
			parent.append( newEle.prop('src',src) );
			loaderAnimation.hide();
		}
	}
	
	return {

		init: function(transactionObj,classes) {
			previewPlugin =  $("."+classes.previewPluginsNames);
			parent = $("#"+classes.previewContainer);
			loaderAnimation = $("#"+classes.loaderAnimationId);
			template = previewPlugin[0].outerHTML;
			transactionObj.setPreviewLoaderAnimation(classes.loaderAnimationId);
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load preview from src source
				if(data.previewLinks !== null && data.previewLinks !== undefined) {
					loadPreview(data.previewLinks);
				} else {
					//no preview link available
				}
			}
		}
	}
}());