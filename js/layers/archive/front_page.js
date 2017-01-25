"USE STRICT"
var FrontPage = (function(previewController,uploadForm,transaction) {
	// var uploadedFile = null; 
	var uploaded = false;
	var signature = null;
	var returnedData = null;
	// var previewLinks = null; //dataType:array

	/*
	* update form and trigger events
	* @param bool - true/false uploaded or not
	* @param data - data from server after ajax request
	*/
 	function setUploaded(bool,data) {
		uploaded = bool;
		returnedData = (bool) ? data : null;
		$("#form").trigger("change"); //notify front page form of changes
		$("#confirm-form").trigger('reset'); //reset confirm form in case of reupload

		if(false == bool) { //old folder, clear transaction
			transaction.clearTransaction();
			console.log('old function');
		} else { //new file uploaded

			//set preview file
			previewController.loadPreview(returnedData.previewLinks);
		}
	}

	/*
	* bind remove button
	*/
	// function bindRemoveBtn() {
	// 	$(".delete-icon").on("click",function(){
	// 		setUploaded( false );
	// 	});
	// }

	/*
	* bind file input field
	*/
	function bindInputField() {
		$("#file_input_field").on("change",function(e){
			var changeE = e || window.event;
			//IE9 & Other Browsers
			changeE.stopPropagation();
			//IE8 and Lower
			changeE.cancelBubble = true;

			if(typeof($("#file_input_field")[0].files[0]) !== 'undefined') { //input is not undefined
				checkAndStore( $("#file_input_field")[0].files[0] );
				//console.log('turning off ajaxloader');
			}

		});
	}	

	/*
	* check file then store 
	* @param file - file to check
	*/
	function checkAndStore(file) {
		//clear recently uploaded file
		if( uploaded == true ){
			setUploaded( false );
		}

		//set new file in model
		transaction.setFile(file,
			function(){
				//turn on loading gif
				$("#ajax-loader1").css("display","block");
			},function(){
				//turn off loading gif
				$("#ajax-loader1").css("display","none");

				//fetchdata
				//console.log(transaction.getTransactionData());
			});

		//check if file is valid
		// try {
		// 	if ( file.size > 10000000) { 
		// 		throw "Please make sure your file is NO MORE than 10MB.";
		// 	} else {
		// 		var fm = new FormData();
		// 		fm.append('file', file);

		// 		//talking to server
		// 		$.ajax({
		// 			data: fm,
		// 			contentType: false,
		// 			processData: false,
		// 			// dataType: 'json',
		// 			url: 'php/phase1/file-screener.php',
		// 			type: 'POST',
		// 			success: function(rawData){
		// 				console.log(rawData);
		// 				//#######
		// 				try {
		// 					var data = JSON.parse(rawData);

		// 					if(data.error === undefined) { //if there are no errors
		// 						//check id data.amount is a number and not = NaN

		// 						//store file 
		// 						that.uploadedFile = file; //bad
		// 						//this.uploadedFile = file; //good
		// 						//initalize slideshow

		// 						//approve file
		// 						setUploaded(true, data);
		// 					} else {
		// 						//alert user
		// 						toggleAlertPrivate("Error!" , data.error , "red", true);
		// 					}
		// 				} catch (e) {
		// 					console.log("Error: " + e);
		// 					console.log(rawData);
		// 				}
		// 			},
		// 			error: function(XMLHttpRequest, textStatus, errorThrown) { 
		// 				//setUploaded(false);
		// 				$("#ajax-loader1").css("display","none");  
		// 		        alert("Error: " + errorThrown + ".\nPlease contact an officer for help.");
		// 		    }
		// 		}).done(function() {
		// 			//turn off loading gif
		// 			$("#ajax-loader1").css("display","none");  
		// 		});

		// 	}
		// } catch (e) { //flag an error message

		// 	//report string errors only
		// 	if (typeof e !== "object") {
		// 		//toggle message 
		// 		toggleAlertPrivate("Error!" ,e ,"red",true);
		// 		setUploaded( false );
		// 	} 

		// 	/*
		// 		else { //debugging purposes only!
		// 			$("#ajax-loader1").css("display","none");
		// 			throw e; //propagate e 
		// 		}
		// 	*/
		// }


	}


	/*
	* bind upload space event
	*/
	function bindUploadSpace() {
		//bind click event
		$("#add_file_space").click( function() {
			//bind input event
			$("#file_input_field").click();	

		}).on("dragover",function(e){
			var changeE = e || window.event; 
			//IE9 & Other Browsers
			changeE.stopPropagation();
			//IE8 and Lower
			changeE.cancelBubble = true;

			changeE.preventDefault();

			//console.log(changeE);
			$("#add_file_space").addClass("dragover");

		}).on("drop",function(e) {

			var changeE = e || window.event;
			//IE9 & Other Browsers
			changeE.stopPropagation();
			//IE8 and Lower
			changeE.cancelBubble = true;
			changeE.preventDefault();

			var file = changeE.originalEvent.dataTransfer.files[0];

			if(typeof(file) !== 'undefined') { //input is not undefined
				checkAndStore( file );
				//console.log('turning off ajaxloader');
			}

			//document.getElementById("file_input_field").files[0] = file;
			//$("#file_input_field").trigger('change');
			//$("#file_input_field")[0] = changeE.originalEvent.dataTransfer;

		}).on("dragleave drop",function(e){
			$("#add_file_space").removeClass("dragover");
		});
	}

	/*
	* toggle file display, show or hide
	*/
	function toggleFileDisplay() {
		var that = this;

		$("#form").on("change",function() {

			//display
			if( uploaded ) {
				var fileName = that.uploadedFile.name;
				
				uploadForm.setFileName(fileName);
				//$("#file_name").text(fileName);

				//$("#uploaded_file_space").css("display","block");
				$("#submit_btn").prop("disabled", false);
			//hide
			} else {
				uploadForm.setFileName("");
				uploadForm.reset();
				$("#submit_btn").prop("disabled", true);
				$("#ajax-loader1").css("display","none"); //turn off ajaxloader
			}
		});

	}

	/*
	* toggle alert modal (Private)
	* @param header - header message
	* @param msg - content message
	* @param headerColor - color of header message
	* @param regular - regular alert - true/false
	*/
	function toggleAlertPrivate(header,msg,headerColor,regular) {
		//$("#alertModal").modal("hide"); //close modal if its opened

		$("#alert-header").text(header).css("color",headerColor);
		//$("#alert-body").text(msg).css("font-weight","bold"); 
		$("#alert-body").html(msg).css("font-weight","bold"); 

		if(!regular) { //alert displays a fatal error requiring permission to store file, permission buttons will be displayed
			$("#resultPrimaryBtn").css('display','none'); 
			$(".permission-btn").css('display','inline');
		} else {												//alert displays a regular FYI error 
			$("#resultPrimaryBtn").css('display','inline'); 
			$(".permission-btn").css('display','none');
		}
		setTimeout(function(){
			$("#alertModal").modal("show");
		},400);
		
	}



	//public methods
	return {
		/*
		* bind UI events on front page
		*/
		bindListeners: function() {
			//bindUploadSpace();
			// bindRemoveBtn();
			//bindInputField();
			toggleFileDisplay();
		},

		/*
		* toggle alert modal (Public)
		* @param h - header message
		* @param m - content message
		* @param hc - color of header message
		* @param r - regular alert - true/false
		*/
		toggleAlert: function(h,m,hc,r) {
			toggleAlertPrivate(h,m,hc,r);
		},

		/*
		* Fetch data about file and user
		* @return returned data on uploaded file
		*/
		fetchData: function(){
			//console.log(this);
			return returnedData;
		},

		/*
		* @return true/false
		*/
		isUploaded: function(){
			return uploaded;
		},

		/*
		* clear form
		*/
		clearForm: function(){
			setUploaded(false);
		},

		/*
		* This function will update view based on the arguments variable provided. 
		* It is called by the notify method from model module
		*/
		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//display file name 
				if(data.fileName !== null && data.fileName !== undefined) {

					uploadForm.setFileName(data.fileName);
					//$("#file_name").text(fileName);

					$("#submit_btn").prop("disabled", false);
				} else { 								//no file name presence
					uploadForm.setFileName("");
					uploadForm.reset();
					$("#submit_btn").prop("disabled", true);
				}
			}

			
		}

	
	}

})(PreviewController,UploadForm,Transaction);