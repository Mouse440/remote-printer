"use strict"
var UploadedFileDisplay = (function(){
	var transactionModel;
	var fileNameSection;
	var fileInfoDisplay;
	var deleteIcon;
	var uploadingGif;
	//var continueBtn;

	function initDeleteIcon(deleteEle) {
		deleteEle.on("click",function(){
			transactionModel.clearTransaction(function(){
						uploadingGif.css("display","block"); //turn on loading gif
					}, 
					function(){
						uploadingGif.css("display","none"); //turn off loading gif
					},
					function(){
						uploadingGif.css("display","none"); //turn off loading gif
					});
		});

		return deleteEle;
	}

	return {
		init: function(transactionObj,ids,classes) {
			transactionModel = transactionObj;
			fileNameSection = $("#"+ids.fileNameId);
			fileInfoDisplay = $("#"+ids.uploadedFileSpaceId);
			deleteIcon = initDeleteIcon( $("."+classes.deleteIcon) );
			uploadingGif = $("#"+ids.uploadingGif);
			//continueBtn = $("#"+ids.continueButtonId);
		},

		updateView: function(){
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//display file name 
				if(data.fileName !== null && data.fileName !== undefined) {

					fileNameSection.text(data.fileName);
					fileInfoDisplay.css("display","block"); //show file display
					//continueBtn.prop("disabled", false);
				} else {//no file name presence
					fileNameSection.text();
					fileInfoDisplay.css("display","none");  //hide file display
					//continueBtn.prop("disabled", true);
				}
			}
		}
	}	
}());