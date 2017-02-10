"use strict"
var PrintStatusPanel = (function(BB){
	var transactionModel;
	var printStatusPanel;
	var jobStatusPanel;
	var printCompleted;
	var isPolling;
	var maxPollTime;
	var transactionData;
	var isDisplaying;

	function cancelCB(d) {
		if(d) {	//print job was sent to printer and wasn't present in print que
			showCompleteMessage(transactionData.printResult);
		} else {
			// console.log('no response');
			showTimeOutMessage(transactionData.allowance);
		}
	}

	function cancelTransaction(data) {
		transactionData = data;

		//clear transaction
		transactionModel.setIsPrinting(false);
		transactionModel.cancelTransaction(null,null,cancelCB); 
	}

	function checkStatus(data) {
		if(!isPolling && !printCompleted) {	//is not polling yet and is not completed yet

			isPolling = true; 	
			var intervalTime = 500;	
			var timeElapsed = 0;
			var interval = setInterval(function(){
				
				timeElapsed += intervalTime;	//log time elapsed
				var stillWithinTimeLimit = maxPollTime > timeElapsed; //times is up or not
				if(!printCompleted && stillWithinTimeLimit) { //print is completed or poll timer is up   
					//poll
					transactionModel.getPrintStatus();
				} else {
					clearInterval(interval);
					isPolling = false;
				}

				if(!stillWithinTimeLimit) {
					cancelTransaction(data);
				}
			}, intervalTime);
		}
	}

	function showTimeOutMessage(pagesLeft){
		if(!isDisplaying) {
			isDisplaying = true;
			BB.dialog({
	  			message: ["<h4>Sorry, your request has timed out. <br>Your allowance has been reset to <span style='color:orange'>"
			  			,pagesLeft,"</span> </h4>"
			  			,"<h4>Please contact an officer for help.</h4>"].join(''),		  
	  		    title: "<h3 style='color:orange;'>Timed Out!</h3>",
			    buttons: {
		  			alert: {
		  				label:'Close'
		  			}
			    }
			});
		}
		isDisplaying = false;
	}

	function showCompleteMessage(allowance) {
		// console.log(printResult);
		if(!isDisplaying) {
			isDisplaying = true;
			BB.dialog({
			  message: ["<h4>Your document has been sent to the printer. <br>You have <span style='color:green'>",allowance,"</span> pages left. </h4>"].join(''),
			  title: "<h3 style='color:green;margin:0 0;'>Success!</h3>",
			  buttons: {
		  		alert: {
		  			label:'Close'
		  		}
			  }
			});
		}
		isDisplaying = false;
	}

	function showPrintIncompletedMessage(printState,oAllowance) {
		if(!isDisplaying) {
			isDisplaying = true;
			BB.dialog({
			  message: ["<h4>Sorry your request was unsuccessful. <br>Your allowance has been reset to <span style='color:orange'>"
			  			,oAllowance,"</span> </h4>"
			  			,"<h4>Please contact an officer for help.</h4>"].join(''),
			  title: Array("<h3 style='color:orange;margin:0 0;'>",printState,"</h3>").join(''),
			  buttons: {
		  		alert: {
		  			label:'Close'
		  		}
			  }
			});
		}
		isDisplaying = false;
	}

	function showUnknownMessage(printState,oAllowance) {
		BB.dialog({
		  message: ["<h4>Sorry something went wrong. <br>Your allowance has been reset to <span style='color:orange'>"
		  			,oAllowance,"</span> </h4>"
		  			,"<h4>Please contact an officer for help.</h4>"].join(''),
		  title: Array("<h3 style='color:orange;margin:0 0;'>",printState,"</h3>").join(''),
		  buttons: {
	  		alert: {
	  			label:'Close'
	  		}
		  }
		});
	}

	// function displayStatus(status) {
	// 	jobStatusPanel.text(status);
	// }

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			printStatusPanel = $("#"+ids.printStatusPanelId);
			jobStatusPanel = $("#"+ids.jobStatusPanelId);
			printCompleted = false;
			isPolling = false;
			printCompleted = false;
			isDisplaying = false;
			maxPollTime = 10000; //poll time in milliseconds
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];
				var isPrinting = data.isPrinting;
				// console.log(data);
				//printing state
				if(isPrinting == true) {
					printStatusPanel.fadeIn(200);
					printStatusPanel.show();
				} else if (isPrinting == false || isPrinting == undefined || isPrinting == null){
					printStatusPanel.hide();
					jobStatusPanel.text('');
				}

				if(data.printResult === undefined || data.printResult === null) {
					jobStatusPanel.text('');
				} else {
					//get status
					var requestState = data.printResult.statusMessage;
					var oAllowance = data.allowance;

					// console.log(data);

					jobStatusPanel.text(requestState);

					//display status
					if(requestState.match(/completed/ig)) { //print completed
						transactionModel.clearModel();			//clear transaction data from model
						printCompleted = true; 					//turn off polling
						showCompleteMessage(data.printResult.pagesLeft);

						if(isPrinting) {	
							transactionModel.setIsPrinting(false);
						}
					} else if(requestState.match(/processing|pending/ig)) { //print processing, pending
						printCompleted = false;
						console.log('print_job is processing');
						//checkStatus(data);
					} else if(requestState.match(/stopped|canceled|aborted/ig)) { //print stopped, canceled, or aborted
						if(isPrinting) {			//prevent second round execution
							transactionModel.setIsPrinting(false);
							transactionModel.clearModel();
							printCompleted = true;
							showPrintIncompletedMessage(requestState,oAllowance);
						}
					} else if(requestState.match(/held|unknown/ig)) { //print held or unknown, this is an edge case 
						if(isPrinting) {
							transactionModel.setIsPrinting(false);
							transactionModel.clearModel();
							printCompleted = true;
							showUnknownMessage(requestState,oAllowance);
						}
					}
				}
				// console.log(data);
			}
		}

	}
}(bootbox));