"use strict"
/*
* This module is for pdf preview controller 
*/
var PreviewController = (function(pluginID) {
	var previewPlugin = $(pluginID);

	return {
		/*
		* Initialize the preview
		* @param src - the source of preview file
		*/
		loadPreview: function(src) {
			previewPlugin.prop('src',src);
		}
	}

})('.preview-plugin');