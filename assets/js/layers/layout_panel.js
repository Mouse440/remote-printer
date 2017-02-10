"use strict"
var LayoutPanel = (function(){
	var transactionModel;
	var portraitEle;
	var landscapeEle;

	function setLayout() {
		var layout = $(this).val();
		if(transactionModel.setLayout(layout)){
			transactionModel.getNewPreviewLink();
		} 
	}

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			portraitEle =   $("#"+ids.radioPortraitId).on('click',setLayout);
			landscapeEle =   $("#"+ids.radioLandscapeId).on('click',setLayout);
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load layout
				if(data.layout === 'portrait') {
					portraitEle[0].checked = true;
				} else if(data.layout === 'landscape'){
					//no allowance
					landscapeEle[0].checked = true;
				} else {
					portraitEle[0].checked = true;
					//console.log('Warning: layout is ambiguous -- ' + data.layout);
				}
			}
		}

	}
}());