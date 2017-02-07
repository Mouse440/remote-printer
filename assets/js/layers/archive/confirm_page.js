"USE STRICT"
var ConfirmPage = (function(fpage,previewController){

	var serverData = {
		amount: "",
		allowance: "",
		previewLinks: ""
	};
	var delayTime = 400; //in milliseconds
	/*
	* bind submit button
	*/
	function bindSubmitButton(){
		$("#submit_btn").on("click", function(e){
			e.preventDefault();
			if( fpage.isUploaded() ) {
				//serverData = fpage.fetchData();

				
				if(parseInt(serverData.allowance) > 0) {

					// //fill in allowance
					// var text = (parseInt(serverData.allowance) == 1) ? ' page' : ' pages'; 
					// $("#allowance").text(serverData.allowance + text); //fill in allowance 
					// $("#allowance-description").text(text); 		//fill in allowance description

					//initial trigger to update confirm form
					$("#confirm-form").trigger("change");

					//update total pages
					$("#myModal").modal("show");

				} else {
					//display error for 0 pages left
					msg = '<h4>Sorry you have '+ "<span style='color:orange'>0</span>"+ ' prints left.</h4>' + 
							'<h4>Your allowance will be refreshed on Sunday.</h4>';

					fpage.toggleAlert('No can do!',msg,'orange',true); //toggle alert
					_resetAllForms();
				}
			} else {
				//tell user to upload a file
			}
		});
	}

	/*
	* bind confirm form change event
	*/
	function bindConfirmFormChangeEvent() {
		$("#confirm-form").on("change",function() {
			//fill in total
			var total = _getTotal();
			
			console.log(total);

			var color = (total > serverData.allowance || total === 0) ? 'red' : 'green';
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
			}								
		});
	}

	/*
	* reset confirm form
	*/
	function _resetAllForms(){
		$("#confirm-form").trigger('reset');//reset form
		$("#ajax-loader1").css("display","none"); //turn off ajax-loader
		$("#jobStatus").html('');

		//reset front page form
		fpage.clearForm();
	}

	/*
	* Converts criptic status numbers into meaningful messages
	*/
	function statusMsgConverter(statusNum) {
		switch ( parseInt(statusNum) ) {
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
	* bind print button event
	* NOTE: it will send data to server
	*/
	function bindPrintButton() {
		$("#printBtn").on("click",function() {
			var printOrder = _fetchPrintOrder();  //fetch printorder 

			var formData = new FormData(); 

			//packaging data
			$.each(printOrder, function(index, value){
				formData.append(index , value);
			})

			formData.append("total", $("#total").val() ); //add total field to it
			// formData.append("file", window.uploadedFile ); //add file 
			formData.append("amount", serverData.amount);
			formData.append("allowance", serverData.allowance);
			formData.append("action",'print');

			/* EXAMPLE OF DATA SENT
			Array --- post array
			(
			    [range] => 1-8
			    [copies] => 1
			    [twoSided] => false
			    [layout] => portrait
			    [total] => 8
			    [amount] => 8
			    [allowance] => 10
			)
			Array --- file array
			(
			    [name] => www.cs.unca.edu_~bruce_Fall11_255_PIC24_instruction_set_summary.pdf
			    [type] => application/pdf
			    [tmp_name] => /tmp/phpdElZZB
			    [error] => 0
			    [size] => 190881
			)*/

			//turn on ajax loader gif
			$("#modal-processing-div").css("display","block");

			//send data to server
			var xhr = $.ajax({
				data : formData,
				contentType: false,
				processData: false,
				type: "POST",
				url: "php/phase2/processor.php",
				// dataType: 'json',
				success: function(odata) {

					try {
						var data = JSON.parse(odata);

						if(typeof data.error == 'undefined') { //no error detected
							if(data.status === 'completed') { //print is completed 
								__handleDoneResponse(data);
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
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
						_resetAllForms();
						__clearConfirmForm();
				        alert("Error: " + errorThrown); 
				        //console.log(errorThrown);
			    },
			});
		
			//wait 10 seconds if no response turn off ajax-gif and turn on error modal
			/*function _checkTimeOut() {
				setTimeout(function(){
					$("#modal-processing-div").css("display","none");
					$("#myModal").modal("hide");
					fpage.toggleAlert("Request Timeout",
										"The server has not respond to your request. " + 
										"Your print job might not have been printed successfully. " + 
										"Please notify an officer for help.", "Orange");
				}
			}*/
		}); 
	

		/*
		* Clear confirm form from modal view
		* Action: turn off ajax loader gif and hide modal
		*/
		function __clearConfirmForm() {
			//turn off ajax-loader
			$("#modal-processing-div").css("display","none");
			$("#myModal").modal("hide");
		}
	
		/*
		* do a request to the server to check status
		*/
		function __checkPrintStatus(jobId) {
			//console.log('called print status');

			var formData = new FormData(); 
			formData.append('jobId', jobId);
			formData.append('action','check');
			
			$.ajax({
				data: formData,
				contentType: false,
				processData: false,
				type: 'POST',
				url: "php/phase2/processor.php",
				// dataType: 'json',
				success: function(odata) {
					// console.log(odata);
					try {
						var data = JSON.parse(odata);
						if(data.status === 'success') { //print is completed 
							__handleDoneResponse(data);
						} else if(data.status === 'canceled') { // status is canceled
							fpage.toggleAlert('Failed', data.msg, 'red',true);
							_resetAllForms();
							__clearConfirmForm();
						}
					} catch (e) {
						console.log(odata);
						alert("Error: " + e); 
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
						_resetAllForms();
						__clearConfirmForm();
				        alert("Error: " + errorThrown); 
			    }
			});
		}
		
		/*
		* handle done response 
		* @param data - response from server
		*/
		function __handleDoneResponse(data){
			var msg = '<h4>You have ' + "<span style='color:green'>" + data.pagesLeft + '</span>' + ' pages left. </h4>'; // success message
			fpage.toggleAlert('Success!', msg ,'green',true); //toggle alert
			_resetAllForms();
			__clearConfirmForm();
		}
	}


	/*
	* calculate the total field with coherence to form inputs
	*/
	function _getTotal() {
		var input = _fetchPrintOrder(); //fetch inputs

		var copies = parseInt( input["copies"] );

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
		}
	}

	/*
	* fetch confirm form inputs
	* @param - array of inputs from confirm form
	*/
	function _fetchPrintOrder() {
		//Collecting pages information
		var pageRange = ( $("#radio-all").is(":checked") ||  //radio all is checked
						   $("#page-range").val() === ""  ) ? "1-" + serverData.amount : $("#page-range").val(); //input is empty

		if( pageRange == "1-1")	{ //common case
			pageRange = "1";
		}

		if ( $("#pages-options").hasClass("has-error") ) { //special case when page range input is invalid
			pageRange = false;
		}

		//Collecting copies information
		var copiesAmount = null;
		copiesAmount = ($("#copies-amount").val() === "") ? 1 : $("#copies-amount").val();

		//Collecting two-sided information
		var twoSided = false;
		twoSided = $("#two-sided-box").prop("checked");

		//Collecting layout information
		var layout = "portrait";
		layout = ($("#radio-portrait").prop("checked") == true) ? $("#radio-portrait").val() : $("#radio-landscape").val() ;

		return {	   "range" : pageRange,
				      "copies" : copiesAmount,
					"twoSided" : twoSided,
					  "layout" : layout 
					};
	}

	/*
	* Link focus on radio and textfield element 
	* @radio - JQuery element of radio button
	* @textfield - Jquery element of textfield
	*/
	function linkFocus(radio,textfield) {

		//link textfield with radio button
		textfield.on("focus", function() {  
			radio.prop("checked", true); 
		});

		//link radio with textfield
		radio.on("change", function() { 
	   		textfield.focus();
		});
	}

	/*
	* bind copy incrementing-buttons
	*/
	function bindIncrButtons(){
		var copyTimer = null;

		$(".incr-btn").on("click", function(){
			var incrVal = ($(this).prop("value") == "true") ? 1 : -1;
			var newCopyAmount = parseInt( $("#copies-amount").prop("value") ) + incrVal; 
			if( newCopyAmount <= 999 && newCopyAmount > 0 ){
				$("#copies-amount").prop("value", newCopyAmount );
				$("#confirm-form").trigger("change");
			}
		});
	}

	/*
	* bind copy input field event
	*/
	function bindCopyInputEvent(){
		$("#copies-amount").on("keypress", function(e){
			var keyCode = e.which;
	   		var character = String.fromCharCode(keyCode);
	   		var regex = /[0-9]/;
	   		if( !regex.test(character) ) {
	   			event.preventDefault();
   			}
   			//console.log("triggered");
		}).typing({
			stop: function(evnt, ele) {
				$("#confirm-form").trigger("change");
			}, delay: delayTime
		}).on('blur',function(){ //fill in 1 if empty when blur
			if($(this).val() === "") {
				$(this).val(1);
			}
		});
	}
	

	/*
	* bind range input field event
	*/
	function bindRangeInputEvent(){

		//bind radio-all radio 
		$("#radio-all").on("focus",function() {
			$("#confirm-form").trigger("change");

			if ( $("#pages-options").hasClass("has-error") ){
				_toggleAlert();  //turn off alert 
			}
		});

		//bind page-range field
		$("#page-range").typing({
			stop: function(e, elem) {
				//check input
				_validateInput(elem);
				$("#confirm-form").trigger("change");
			}, delay: delayTime
		}).on("blur", function() {
			if( $("#pages-options").hasClass("has-error") && $("#radio-all").is(":checked") ){
				_toggleAlert();
			}
		}).on("focus",function() {
			//check for pre-existing invalid inputs
			_validateInput($("#page-range"));
			
		});

		/*
		* Validating input
		* @param - affected element
		*/
		function _validateInput(elem) {
			//Validating input
   			try{
   				_checkInput(elem);
   				//$("#pages-options").prop("isValid",true); //set isValid true
   				if($("#pages-options").hasClass("has-error") ){
   					_toggleAlert();
   				}
   			} catch (e) {
   				//$("#pages-options").prop("isValid",false); //set isValid false
   				if(!$("#pages-options").hasClass("has-error") ){
   					_toggleAlert();
   				}
   			}
		}

		/*
		* toggle page range error alert
		*/
		function _toggleAlert() {
			$("#pages-options").toggleClass("has-error"); 
			$("#pages-options-error").toggleClass("transition-change");
		}
		
		/*
		* check input
		* @param e - JQuery element 
		*/
		function _checkInput(e){

			/*//debug only
			return;*/

			var input = e[0].value;

			if( input.length === 0) { //special case when text field is empty
				return;
			}

	 		var hasRightFormat = /^([1-9]{1}[\d]*)([\-\,]?)([1-9]?[\d]*[\,]?)(\1*([\-\,]?)([1-9]{1}[\d]*)*)*/gm.test(input); //general screening
			var hasLastNum = /[0-9]/.test(input.charAt(input.length-1)); //test for last number
			var hasOnlyAllowedCharacters = /[0-9]+[\-\,]*/.test(input); //test for invalid characters

			if( hasRightFormat && hasLastNum && hasOnlyAllowedCharacters) { //test for general format
				var containDash = /\-/.test(input);
   				var containComma = /\,/.test(input);

   				if(containDash || containComma) { //check if input has more than 1 number
   					var uniformString = input.replace(/\,/g,"-"); //turn the input into uniform format to split
   					var pageRangeArr = uniformString.split("-"); //create an array of just numbers
   				

   					for( var i = 1; i < pageRangeArr.length; i++ ) { //check for consistent number range (smallest to largest)
   						var prevNum = parseInt( pageRangeArr[i-1] );
   						var currentNum = parseInt( pageRangeArr[i] );

   						if(i == pageRangeArr.length-1) { // check for last number being single page and larger than max 
   							var pivot = input.indexOf(currentNum);
   							if( input.charAt(pivot-1) == ',' && currentNum > serverData.amount) {
   								throw "Invalid last number";
   							}
   						}

   						//Check for valid placements and values
   						if( isNaN(prevNum) || isNaN(currentNum) || (prevNum >= currentNum) || (prevNum > serverData.amount) ) { 
   							throw "Invalid number order";
   						}
   					}
   				} else { //Input contains only 1 multiple digit number
   					var inputValue = parseInt(input);
   					if(inputValue > serverData.amount) { //check if the input value is in range of document total pages
	   					throw "Invalid input";
   					} 
   				}
			} else {
				throw "Invalid format"; 
			}
		}
	}

	return {
		/*
		* event listeners
		*/
		bindListeners: function() {
			bindConfirmFormChangeEvent();
			bindSubmitButton();
			bindIncrButtons();
			bindCopyInputEvent();
			bindRangeInputEvent();
			bindPrintButton();
			linkFocus($("#radio-other"), $("#page-range"));
		},

		//update confirm page view
		updateView: function() {
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				if(data.amount !== null && data.amount !== undefined) {
					serverData.amount = data.amount;
					console.log("Confirm Page view: ",data.amount);
				}

				//display allowance
				if(data.allowance !== null && data.allowance !== undefined) {
					//fill in allowance
					var text = (parseInt(data.allowance) == 1) ? ' page' : ' pages'; 
					$("#allowance").text(data.allowance + text); //fill in allowance

					serverData.allowance = data.allowance;
					console.log("Confirm Page view: ",$("#allowance").text()); 
				} 

				//preview links
				if(data.previewLinks !== null && data.previewLinks !== undefined) {
					//load preview from link
					previewController.loadPreview(data.previewLinks);
					serverData.previewLinks = data.previewLinks;
					console.log("Confirm Page view: ",data.previewLinks);
				}
			}

			
		}
	}
})(FrontPage,PreviewController);