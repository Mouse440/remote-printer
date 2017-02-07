"use strict"
/* DEPRECATED*/
var ConfirmationForm = (function(){
	var transactionModel;
	var confirmFormEle;
	var totalEle;
	var allowanceEle;
	var copiesEle;
	var pagerangeEle;
	var layoutEle;
	var twoSidedEle;

	/*
	* calculate the total field with coherence to form inputs
	*/
	function getTotal() {
		//var input = _fetchPrintOrder(); //fetch inputs

		var copies = parseInt( copiesEle.value() );

		console.log(copies);

		/*
		if( !input["range"] ) {
			return 0;
		} else {
			var array = input["range"].split(","); //Turn input into nodes
			var result = 0;
			for( var i in array ) {
				var node = array[i]; 
				if(/\-/.test(node)) { //test if node is a range of numbers
					var dashIndex = node.indexOf("-");
					var firstNum = parseInt(node.substring(0 , dashIndex));
					var secondNum = parseInt(node.substring(dashIndex+1, node.length));
					secondNum = (secondNum > serverData.amount) ? docTotalPages : secondNum; //Check for the case when last number is larger than lastpage

					result += secondNum-firstNum+1;
				} else { //node contains only a number
					result += 1;
				}
			}
			return result*copies;
		}*/
	}

 	/*
	* bind confirm form change event
	*/
	function attachFormListener(formEle) {

		formEle.on("change",function() {
			//fill in total

			var total = getTotal();
			
			console.log('total');
			/*
			var color = (total > transactionModel.getTransactionData().allowance || total === 0) ? 'red' : 'green';
			var totalText = (total === 1) ? ' page' : ' pages';  
			$("#total").html(total +totalText)
						.css('color', color ).val(total);	//set color of text for approved or unapproved 

			// $("#total-description").html(totalText)			//setting total description
			// 			.css('color', color );	
			
			//enable/disable print button
			if( color === 'red' || total === NaN) {
				$("#printBtn").prop("disabled", true);
			} else {
				$("#printBtn").prop("disabled", false);
			}	*/							
		});

		return formEle;
	}

	return {

		init: function(transactionObj,ids,subModules) {
			transactionModel = transactionObj;
			confirmFormEle = $("#"+ids.formId);
			totalEle = 		 $("#"+ids.totalId);
			allowanceEle =   $("#"+ids.allowanceId);
			// copiesEle = 	 $("#"+ids.copiesId);
			copiesEle 	 =   subModules.copiesPanelModule;
			pagerangeEle = 	 $("#"+ids.pagerangeId);
			layoutEle = 	 $("#"+ids.layoutSpaceId);
			twoSidedEle = 	 $("#"+ids.twoSidedId);
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load total
				if(data.amount !== null && data.amount !== undefined && data.allowance !== null && data.allowance !== undefined) {

					var color = (data.amount > data.allowance || data.amount === 0) ? 'red' : 'green';
     				var totalText = (data.amount === 1) ? ' page' : ' pages';  

					totalEle.html(data.amount+totalText).css('color', color);
				} else {
					//no total
					totalEle.html('N/A');
				}

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