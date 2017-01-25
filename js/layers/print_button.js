"use strict"
var PrintButton = (function(){
	var transactionModel;
	var printBtn;
	var totalPanel;
	// var printStatusPanel;

	function initPrintBtn(btn){
		btn.on('click',function(){
			transactionModel.executePrint();
		});

		return btn;
	}

	return {

		init: function(transactionObj,ids,totalPanelModule) {
			transactionModel = transactionObj;
			printBtn = initPrintBtn( $("#"+ids.printBtnId) );
			// printStatusPanel = $("#"+ids.printStatusPanelId);
			totalPanel = totalPanelModule;
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];
				var total = totalPanel.getTotal(data);
				if(total > data.allowance || total === NaN || total <= 0) {
					printBtn.prop("disabled", true);
					// printBtn.attr('disabled', true); //Safari compatibility
				} else {
					printBtn.prop("disabled", false);
					// printBtn.attr("disabled", false); //Safari compatibility
				}
			}
		}

	}
}());