"use strict"
var PageRangePanel = (function(){

	var radioAllEle;
	var radioOtherEle;
	var pageRangeEle;
	var pageRangeErrorEle;
	var pageOptionsDiv;
	var transactionModel;

	//bind radio-all option
	function bindRadioAllBtn(radAllEle) {
		radAllEle.on("focus change",function(e) {
			var amnt = transactionModel.getTransactionData().amount;
			var range = (amnt == 1)? '1' : '1-'+amnt;

			pageRangeErrorEle.slideUp(200);
			pageOptionsDiv.removeClass('has-error');

			if(transactionModel.setPageRange(range)){
				transactionModel.getNewPreviewLink();
			} 
		});	

		return radAllEle;
	}

	/*
	* Link focus on radio and textfield element 
	* @radio - JQuery element of radio button
	* @textfield - JQuery element of textfield
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

	//bind page-range field
	function bindPageRangeInput(inputEle) {
		inputEle.typing({
			stop: function(e, elem) {
				inputController(elem);
			}, delay: 400
		}).on("focus",function() {
			inputController($(this));
		});

		return inputEle;
	}	

	/*
	* Controller for checking input validity
	*/
	function inputController(ele) {
		var input = checkInput(ele);

		//check for pre-existing invalid inputs
		if( input ) {
			pageRangeErrorEle.slideUp(200);
			pageOptionsDiv.removeClass('has-error');
			if ( typeof input === 'string' ) {
				if(transactionModel.setPageRange(input) ) {
					transactionModel.getNewPreviewLink();
				}
			}
		} else {
			pageRangeErrorEle.slideDown(300);
			pageOptionsDiv.addClass('has-error');
			transactionModel.setPageRange(NaN);
		}
	}

	/*
	* check pagerange input to match valid format, e.g. 1,3,5-7
	* @param e - JQuery element 
	*/
	function checkInput(e){

		/*//debug only
		return;*/

		var input = e[0].value;
		var documentAmount = transactionModel.getTransactionData().amount;

		if( input.length === 0) { //special case when text field is empty
			return true;
		}

 		var hasRightFormat = /^([1-9]{1}[\d]*)([\-\,]?)([1-9]?[\d]*[\,]?)(\1*([\-\,]?)([1-9]{1}[\d]*)*)*/gm.test(input); //general screening
		var hasLastNum = /[0-9]/.test(input.charAt(input.length-1)); //test for last number
		var hasOnlyAllowedCharacters = !/[^\d,-]/.test(input); //test for invalid characters


		if( hasRightFormat && hasLastNum && hasOnlyAllowedCharacters) { //test for general format
			var containDash = /\-/.test(input);
				var containComma = /\,/.test(input);

				if(containDash || containComma) { //check if input has more than 1 number

					var sectionArr = input.split(",");//check for edge case a-b-c, e.g. 1-2-5
					for(var i in sectionArr) {
						var numOfNum = sectionArr[i].split("-").length;
						if( numOfNum > 2 ) {
							return false;
						}
					}

					var uniformString = input.replace(/\,/g,"-"); //turn the input into uniform format to split
					var pageRangeArr = uniformString.split("-"); //create an array of just numbers

					for( var i = 1; i < pageRangeArr.length; i++ ) { //check for consistent number range (smallest to largest)
						var prevNum = parseInt( pageRangeArr[i-1] );
						var currentNum = parseInt( pageRangeArr[i] );

						if(i == pageRangeArr.length-1) { //last index
							var pivot = input.indexOf(currentNum);
							/*if( input.charAt(pivot-1) == ',' && currentNum > documentAmount) { // check for last number being single page and larger than max 
								// console.log("Invalid last number");
								return false;
							}*/
							if(currentNum > documentAmount) { //check if the last number is larger than max
								return false;
							}
						}

						//Check for valid placements and values
						if( isNaN(prevNum) || isNaN(currentNum) || (prevNum >= currentNum) || (prevNum > documentAmount) ) { 
							// console.log("Invalid number order");
							return false;
						}
					}
				} else { //Input contains only 1 multiple digit number
					var inputValue = parseInt(input);
					if(inputValue > documentAmount) { //check if the input value is in range of document total pages
   						// console.log("Invalid input");
   						return false;
					} 
				}
		} else {
			// console.log("Invalid format"); 
			return false;
		}

		return input;
	}
	 
	return {
		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			radioAllEle = bindRadioAllBtn( $("#"+ids.radioAllInputId) );
			radioOtherEle = $("#"+ids.radioOtherInputId);
			pageRangeEle = bindPageRangeInput( $("#"+ids.pageRangeInputId) );
			pageRangeErrorEle = $("#"+ids.pageRangeErrorId);
			pageOptionsDiv = $("#"+ids.pagesOptions);

			linkFocus(radioOtherEle,pageRangeEle);
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load total
				if(data.pageRange === null || data.pageRange === undefined) {
					//reset page option
					radioAllEle[0].checked = true; 

					pageRangeErrorEle.slideUp(200);	//hide error
					pageOptionsDiv.removeClass('has-error');	

					pageRangeEle.val(''); //clear text 
				} else {
					//console.log(data.pageRange);
				}
			}
		}
	}
}());