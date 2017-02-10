"use strict"
var UploadZone = (function(){

	var transactionModel;
	var jqUploadSpace; 
	var jqDestinationInput;
	var formEle;

	/*
	* bind upload space event
	*/
	function bindUploadSpace(uploadSpace) {
		
		//bind click event
		uploadSpace.click( function() {
			//bind input event
			jqDestinationInput.click(); 

		}).on("dragover",function(e){
			
			ignoreEvent(e);

			//console.log(changeE);
			uploadSpace.addClass("dragover");

		}).on("drop",function(e) {

			var changeE = ignoreEvent(e);

			var file = changeE.originalEvent.dataTransfer.files[0];

			if(typeof(file) !== 'undefined') { //input is not undefined				
				//set new file in model
				transactionModel.setFile(
					file, 
					function(){
						$("#ajax-loader1").css("display","block"); //turn on loading gif
					}, 
					function(){
						$("#ajax-loader1").css("display","none"); //turn off loading gif
					});
			}
		}).on("dragleave drop",function(e){
			uploadSpace.removeClass("dragover");
		});

		return uploadSpace;
	}

	
	/*
	* Bind file input field event
	*/
	function bindInputField(destinationInputId) {
		

		destinationInputId.on("change",function(e){
			ignoreEvent(e);
			
			var file = destinationInputId[0].files[0];
			if(typeof(file) !== 'undefined') { //input is not undefined
				//set new file in model
				transactionModel.setFile(
					file, 
					function(){
						$("#ajax-loader1").css("display","block"); //turn on loading gif
					}, 
					function(){
						$("#ajax-loader1").css("display","none"); //turn off loading gif
					});
				//checkAndStore( jqFileInputEle[0].files[0] );
				//console.log('turning off ajaxloader');
			}

		});

		return destinationInputId;
	}

	/*
	* Clear input file
	*/
	function clearInputFile(){
		var pureJSInputELe = jqDestinationInput[0];

        if(pureJSInputELe.value){
            try{
                pureJSInputELe.value = ''; //for IE11, latest Chrome/Firefox/Opera...
            }catch(err){}

            if(pureJSInputELe.value){ //for IE5 ~ IE10
                var form = formEle[0];
                var ref = pureJSInputELe.nextSibling;

                form.appendChild(pureJSInputELe);
                form.reset();
                ref.parentNode.insertBefore(pureJSInputELe,ref);
            }
        }
    }	

    function ignoreEvent(e){
		var changeE = e || window.event; 
		//IE9 & Other Browsers
		changeE.stopPropagation();
		//IE8 and Lower
		changeE.cancelBubble = true;

		changeE.preventDefault();

		return changeE;
	}

	return {
		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			jqUploadSpace = bindUploadSpace( $('#'+ids.uploadSpaceId) ); 
			jqDestinationInput = bindInputField( $('#'+ids.fileInputId) );
 			formEle = $("#"+ids.uploadFormId);	

			
			
		},

		updateView: function(){
			//update nothing in view

			//clear input field
			clearInputFile();
		}
	}
}());