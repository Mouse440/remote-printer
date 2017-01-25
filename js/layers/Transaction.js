"use strict"
/*
* Transaction is an entity object that holds printing related data about the user and file
* Note this object does not handle any view functionality. Only data.
*/
var Transaction = (function(ObsEngine){
	var ObserverEngine;
	var fileObj;
	var transactionData;
	var errors;
	var previewLoaderAnimation;
	var previewXHRs = Array();
/*
	var TRANSACTION_STATE = {
		0 : "ready",
		1 : "uploading",//not used
		2 : "uploaded",
		3 : "uploadFailed",
		4 : "printing",
	}
*/

	function init() {
		ObserverEngine = new ObsEngine();
		fileObj = {};
		transactionData = {	
							copies: null,
							pageRange: null,
							allowance: null,
							amount: null,
							previewLinks: null,
							printResult: null,
							isPrinting: false,
							// state: TRANSACTION_STATE['0'],
							fileSizeError: null,
							//printingError: null
						};
		errors = {};
		previewLoaderAnimation = null;
	}
	/*
	* Contact server to validate and fetch file and user related information
	* @param file Object Uploaded file object
	* @param beforeSendCB Function Before send call back
	* @param successCB Function Success Call back
	* @param alwaysCB Function Alway function that is executed regardless of result
	* @return Bool true if file is clean, false if otherwise
	*/
	function validateAndFetch(file,beforeSendCB,successCB,alwaysCB) {
		//check if file is within size 
		if ( file.size > 10000000 ) { 
			transactionData.fileSizeError = "Please make sure your file is NO MORE than 10MB.";
		} else {
			var fm = new FormData();
			fm.append('file', file); 
			transactionData.fileSizeError = null;

			// talking to server
			$.ajax({
				data: fm,
				contentType: false,
				processData: false,
				url: 'php/phase1/validateAndFetch.php',
				type: 'POST',
				beforeSend: function() {
					beforeSendCB();
				},
				success: successCB,
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
					//setUploaded(false);
					// $("#ajax-loader1").css("display","none");  
			        alert("Error: " + errorThrown + ".\nPlease contact an officer for help.");
			    }
			}).always(alwaysCB);

		}
		//console.log("line 77");
		ObserverEngine.notify(transactionData);	
	}

	function fetchFileSuccessCB(file) {

		// var that = this;
		return function(rawData) {
			//#######
			try {
					var data = JSON.parse(rawData);

					if(data.error === undefined) { //if there are no errors
						//check id data.amount is a number and not = NaN

						//store file 
						fileObj = file; //bad

						//approve file
						transactionData.amount = data.amount;
						transactionData.allowance = data.allowance;
						transactionData.previewLinks = data.previewLinks;
						transactionData.copies = '1';
						transactionData.pageRange = (data.amount == 1) ? '1' : '1-'+data.amount; //1-last page
						transactionData.layout = 'portrait';
						transactionData.twoSided = true;
						transactionData.fileName = fileObj.name;
						transactionData.total = 0;

					} else {
						//wipes this transaction data
						transactionData = {};
						
						//alert user
						alert(data.error);

						//toggleAlertPrivate("Error!" , data.error , "red", true);
					}
				} catch (e) {
					console.log("Error: " + e);
					console.log(rawData);
				}

				//NOTIFY OBSERVERS
				//console.log("line 122");
				ObserverEngine.notify(transactionData);	
		}
	}


	function executePrint(beforeSendCB) { //execute print function
		// console.log('execute print');

		/* EXAMPLE OF DATA SENT
		Array --- post array
		(
		    [range] => 1-8
		    [copies] => 1
		    [twoSided] => false
		    [layout] => portrait
		)*/

		var formData = new FormData(); 
		formData.append('range', transactionData.pageRange);
		formData.append('copies', transactionData.copies);
		formData.append('twoSided', transactionData.twoSided);
		formData.append('layout', transactionData.layout);
		formData.append('total', transactionData.total);


		//send data to server
		$.ajax({
			data : formData,
			contentType: false,
			processData: false,
			type: "POST",
			url: "php/phase2/processor.php",
			beforeSend: function() {
				transactionData.isPrinting = true;
				//console.log("line 157");
				ObserverEngine.notify(transactionData);	
			},
			success: function(odata) {
				try {
					var data = JSON.parse(odata);
					
					if( data.error ) { //an error has occurred 
					
						// clearModel();
						console.log(data.error);
						alert('An error has occured: ' + data.error);

					} else if ( data.status ) { //no error detected
						data.statusMessage = statusMsgConverter(data.status);
						transactionData.isPrinting = (data.statusMessage == 'Completed.') ? false : true; //done case
						transactionData.printResult = data;

						//console.log('179');
						ObserverEngine.notify(transactionData);
					}
				} catch (e) {
					console.log(odata);
					alert("Error: " + e);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
					alert("Error: " + errorThrown);
			        console.log(errorThrown);
		    },
		});
	}

	function getNewPreviewLink() {

		//send new data options for new preview link
		var data = {
			'pageRange' : transactionData.pageRange,
			'layout' : transactionData.layout
		};

		$.ajax({
			url: 'php/sub_phases/getPreviewDocLink.php',
			type: 'POST',
			data: data,
			beforeSend:  previewRequestController,
			error: function(XMLHttpRequest, textStatus, errorThrown) { 
					if(textStatus != 'abort') {
						// alert("Error: " + errorThrown);
			        	console.log(errorThrown);
			        	console.log(textStatus);
			        }
		    },
		}).done(function(d){
			//update previewLink
			transactionData.previewLinks = d;
			//console.log("line 215");
			ObserverEngine.notify(transactionData);	
		});

		return true;
	}

	function previewRequestController(jqXHR){
		if(previewXHRs.length > 0) { //has xhrs
			abortRequests(previewXHRs);
		} 
		previewXHRs.push(jqXHR);
		previewLoaderAnimation.fadeIn(200);
	}

	//abort requests
	//@param index - delete specific xhr
	function abortRequests(xhrs,index){
		if(index) {
			if (index > -1) {
            	xhrs.splice(index, 1);
        	}
        } else {	//abort all
			$.each(xhrs,function(key,val){
				val.abort();
				xhrs.splice(key,1);
			});
		}

		
	}

	/*
	* Check server for current print status
	*/
	function getPrintStatus() {

		/*GCP STATUS CHECKER NEEDED*/

		// $.ajax({
		// 	type: 'POST',
		// 	url: "php/sub_phases/checkTransactionStatus.php",
		// 	success: function(odata) {
		// 		// console.log(odata);
		// 		try {
		// 			var data = JSON.parse(odata);
		// 			var msg = statusMsgConverter(data.status);
		// 			// transactionData.printResult.originalAllowance = data.originalAllowance;

		// 			transactionData.printResult.statusMessage = msg;
		// 			//console.log("line 261");
		// 			ObserverEngine.notify(transactionData);	
		// 		} catch (e) {
		// 			console.log(odata);
		// 			alert("Error: " + e); 
		// 		}
		// 	},
		// 	error: function(XMLHttpRequest, textStatus, errorThrown) { 
		// 	        alert("Error: " + errorThrown); 
		//     }
		// });
	}

	/*
	* Converts criptic status numbers into meaningful messages
	*/
	function statusMsgConverter(statusNum) {
		switch ( parseInt(statusNum) ) {
			case 0:
				return "Demo";
				break;
			case 1:
				return "Pending...";
				break;
			case 2: 
				return "Held.";
				break;
			case 3:
				return "Processing...";
				break;
			case 4:
				return "Stopped.";
				break;
			case 5:
				return "Canceled.";
				break;
			case 6: 
				return "Aborted.";
				break;
			case 7:
				return "Completed.";
				break;
			default:
				return "Unknown status returned: " + statusNum;
				break;
		}
	}

	/*
	* Clear model of the current transaction
	*/
	function clearModel() {
		fileObj = {};
		transactionData = {};

		//notify observers
		//console.log("line 314");
		ObserverEngine.notify(transactionData);	
	}

	return {

		init: init,
/*
		getTransactionStates: function() {
			return TRANSACTION_STATE;
		},*/

		/*
		* Acessor
		* @return Array transaction data
		*/
		getTransactionData: function() {
			return transactionData;
		},

		/*
		* Accessor
		* @return String file name
		*/
		getFileName: function() {
			return fileObj.name;
		},

		/*
		* Accessor 
		* @return - get print status from server
		*/
		getPrintStatus: getPrintStatus,

		/*
		* Accessor
		* @return String request status 
		*/
		getRequestState: statusMsgConverter,

		/*
		* Mutator
		* Set total
		*/
		setTotal: function(value) {
			transactionData.total = value;
		},

		/*
		* Mutator
		* Set number of copies 
		*/
		setCopies: function(value) {
			transactionData.copies = value;
			//console.log("line 368");
			ObserverEngine.notify(transactionData);
		},

		/*
		* Mutator
		* Set pagerange
		* @return true if range is a new value, false if other wise
		*/
		setPageRange: function(range) {
			if(transactionData.pageRange != range) {
				transactionData.pageRange = range;
				//console.log("line 380");
				ObserverEngine.notify(transactionData);
				return true;
			}
			return false;
		},

		/*
		* Mutator
		* Set page layout
		*/
		setLayout: function(layout) {
			if(transactionData.layout != layout){
				transactionData.layout = layout;
				//console.log("line 393");
				ObserverEngine.notify(transactionData);
				return true;
			}
			return false;
		},

		setPreviewLoaderAnimation: function(id) {
			previewLoaderAnimation = $("#"+id);
		},

		/*
		* Mutator
		* Set two sided
		*/
		setTwoSided: function(twoSided) {
			transactionData.twoSided = twoSided;
		},

		setIsPrinting: function(isPrinting) {
			transactionData.isPrinting = isPrinting;
			if(!isPrinting) { //clearing data
				transactionData = {};
				fileObj = {};
			}
			//console.log("line 415");
			ObserverEngine.notify(transactionData);
		},

		/*
		* Set the file to model, a file check with the server will be sent
		* @param file Object Uploaded file object
		* @param beforeSendCB Function Before send call back
		* @param alwaysCB Function Alway function that is executed regardless of result
		* @return Bool true if file is clean, false if otherwise
		*/
		setFile: function(file,beforeSendCB,alwaysCB) {
			var isClean = false; //file is clean flag - isn't used
			var that = this;

			validateAndFetch(file, beforeSendCB, fetchFileSuccessCB(file), alwaysCB);
			return isClean;
		},

		/*
		* Execute print order
		*/
		executePrint: executePrint,

		/*
		* clear the model of the current transaction
		*/
		clearModel: clearModel,

		/*
		* Get new previewlink
		*/
		getNewPreviewLink: getNewPreviewLink,

		clearAllXhrs: function() {
			abortRequests(previewXHRs);
		},

		/*
		* Clear transaction and erase data
		*/
		clearTransaction: function(beforeSendCB,successCB,alwaysCB){
			clearModel();

			//clear transaction in server
			$.ajax({
				url: 'php/sub_phases/clearTransaction.php',
				type: 'POST',
				beforeSend: beforeSendCB,
				success: successCB,	
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
					//setUploaded(false);
			        console.log("Error: " + errorThrown);

			    },
			}).always(alwaysCB);
		},
		/*
		* cancel transaction, erase data, and return allowance
		*/
		cancelTransaction: function(beforeSendCB,successCB,alwaysCB){
			clearModel();

			//clear transaction in server
			$.ajax({
				url: 'php/sub_phases/cancelTransaction.php',
				type: 'POST',
				beforeSend: beforeSendCB,
				success: successCB,	
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
					//setUploaded(false);
			        console.log("Error: " + errorThrown);

			    },
			}).always(alwaysCB);
		},

		/*
		* Add observer
		* @param obj Object that has implented an updateView function
		*/
		addObserver: function(obj) {
			if(typeof obj !== 'object') {
				console.log('Warning: Attempting to add an undefined Observer to the Model.');
			} else if(typeof obj.updateView !== 'function') {
				console.log('Warning: Attempting to add an Observer without \'updateView\' method to the Model.');
				console.log(obj);
			} else {
				ObserverEngine.observe(obj);
			}
		},

		/*
		* Remove observer
		* @param obj Object to remove
		*/
		removeObserver: function(obj) {
			ObserverEngine.unobserve(obj);
		},
	}

})(ObserverEngine);
