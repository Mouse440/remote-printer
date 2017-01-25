"use strict"
var ContinueButton = (function(){
	var transactionModel;
	var continueBtn;
	var targetModal;

	function bindContinueBtn(){
		continueBtn.on("click", function(e){
			e.preventDefault();
			
			if(parseInt(transactionModel.getTransactionData().allowance) > 0) {
				//update total pages
				targetModal.modal("show");
			} else {

				bootbox.dialog({
				  message: '<h4>Sorry you have '+ "<span style='color:orange'>0</span>"+ ' prints left.</h4>' + 
							'<h4>Your allowance will be refreshed on Sunday.</h4>',
				  title: "<h3 style='color:orange;margin:0 0;'>No can do!</h3>",
				  buttons: {
				  		alert: {
				  			label:'Close'
				  		}
				  }
				});
			}
			
		});
	}

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			continueBtn = $("#"+ids.continueButtonId);
			targetModal = $("#"+ids.targetModalId);

			bindContinueBtn();
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];
				
				//check if fileName exist
				if(data.fileName !== null && data.fileName !== undefined) {
					continueBtn.attr("disabled", false);
				} else {//no file name presence
					targetModal.modal("hide");		//hide modal
					continueBtn.attr("disabled", true);
				}
			}
		}

	}
}());