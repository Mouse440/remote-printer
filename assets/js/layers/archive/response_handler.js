"use strict"
var ResponseHanler = (function(){

	/*
	* handle done response 
	* @param data - response from server
	*/
	function handleDoneResponse(data){
		var msg = '<h4>You have ' + "<span style='color:green'>" + data.pagesLeft + '</span>' + ' pages left. </h4>'; // success message
		fpage.toggleAlert('Success!', msg ,'green',true); //toggle alert
		_resetAllForms();
		__clearConfirmForm();
	}

	return {
		handlePrintResponse: function(response){
			try {
				var data = JSON.parse(response);

				if(typeof data.error == 'undefined') { //no error detected
					if(data.status === 'completed') { //print is completed 
						handleDoneResponse(data);
					} else { // status is waiting
						console.log(data);
						var msg = statusMsgConverter(data.status);
						$("#jobStatus").html(msg);
						// __checkPrintStatus(data.jobId);
					}
				} else{	//an error has occurred
					//if the error is a critical error, it is needed to as for file storage confirmation, regularAlert would be false
					var regularAlert = (data.success === false) ? false : true; 

					fpage.toggleAlert('Failed!', data.error ,'red', regularAlert);  				 //toggle alert
					$("#noPermissionBtn").prop('fileName', window.uploadedFile.name); // save file name before clearing all content

					_resetAllForms();
					__clearConfirmForm();
				}
			} catch (e) {
				console.log(odata);
				alert("Error: " + e)
			} 
		},
	}

})();