"use strict"
var AllowancePanel = (function(){
	var transactionModel;
	var allowanceEle;

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			allowanceEle =   $("#"+ids.allowanceId);

		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load allowance
				if(data.allowance !== null && data.allowance !== undefined) {
					var totalText = (data.allowance === 1) ? ' page' : ' pages';  
   		 			allowanceEle.text(data.allowance+totalText);
				} else {
					//no allowance
					allowanceEle.text('N/A');
				}
			}
		}

	}
}());