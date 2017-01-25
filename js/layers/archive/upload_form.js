"use strict"
/*
* This module represent the upload form of in the front page
*/
var UploadForm = (function(formId,transactionObj){
	var formEle = $(formId);
	var fileNameSection = $("#file_name");
	var fileInfoDisplay = $("#uploaded_file_space");
	var fileInputField = $("#file_input_field");

	/*
	* Initialize the file display object
	*/
	(function initFileDisplay() {
		fileInfoDisplay.find(".delete-icon").on("click",function(){
			transactionObj.clearTransaction();
		});
	})();

	/*
	* Clear input file
	*/
	function clearInputFile(){
		var pureJSInputELe = fileInputField[0];

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

	return {
		/*
		* Set a file name to display
		* @param string name of file to display
		*/
		setFileName: function(name) {
			$("#file_name").text(name);

			//if name is truthy value
			if(name) {
				fileInfoDisplay.css("display","block"); //show file display
			} else {
				fileInfoDisplay.css("display","none");  //hide file display
			}	
		},

		/*
		* Reset this form
		*/
		reset: function() {
			//formEle.find("input").val("");

			//clear input file
			var fileObj = fileInputField[0].files[0];
			clearInputFile();
		},

		/*
		* Initialize this object
 		*/
		init: function() {

		}

	}

})("#form",Transaction);