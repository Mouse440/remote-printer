"use strict"
var TotalPanel = (function(){
	var transactionModel;
	var totalEle;

	/*
	* calculate the total field with coherence to form inputs
	* @param data - transaction data from model
	*/
	function _getTotal(data) {
		var input = data; //fetch inputs

		var copies = parseInt( input.copies );
		var range = input.pageRange;
		var amount = data.amount;

		if( !range ) {
			return 0;
		} else {
			var array = range.split(","); //Turn input into nodes
			var result = 0;
			for( var i in array ) {
				var node = array[i]; 
				if(/\-/.test(node)) { //test if node is a range of numbers
					var dashIndex = node.indexOf("-");
					var firstNum = parseInt(node.substring(0 , dashIndex));
					var secondNum = parseInt(node.substring(dashIndex+1, node.length));
					secondNum = (secondNum > amount) ? amount : secondNum; //Check for the case when last number is larger than lastpage

					result += secondNum-firstNum+1;
				} else { //node contains only a number
					result += 1;
				}
			}
			return result*copies;
		}
	}

	/*
	* Set total back to model
	*/
	function setTotal(val) {
		transactionModel.setTotal(val);
	}

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			totalEle = $("#"+ids.totalId);
		},

		getTotal: _getTotal,

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load total
				if(data.amount !== null && data.amount !== undefined && data.allowance !== null && data.allowance !== undefined) {
					var total = _getTotal(data);
					var color = (total > data.allowance || total <= 0) ? '#a94442' : 'green';
     				var totalText = (total === 1) ? ' page' : ' pages'; 

					totalEle.text(total+totalText).css('color', color);
     				setTotal(total); 
					
				} else {
					//no total
					totalEle.text('N/A');
				}
			}
		}

	}
}());