"use strict"
var UploadErrorDisplay = (function(){
	var displayPanel;
	//var errorMsg;
	var transactionModel;
	var errorMsgEle;

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			displayPanel = $("#"+ids.errorDisplayId);
			errorMsgEle = $("#"+ids.errorMsgFieldId);
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];
				
				if(data.fileSizeError !== null && data.fileSizeError !== undefined) {
					errorMsgEle.text(data.fileSizeError);			//attach error
					displayPanel.css("display","block"); 			//show error
					//continueBtn.prop("disabled", false);

				} else {//no file name presence
					errorMsgEle.text();								
					displayPanel.css("display","none"); //hide error
					//continueBtn.prop("disabled", true);
				}
			}
		}

	}
}());