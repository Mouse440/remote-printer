PrinterApp.directivesModule.directive('pagerangeInput', function() {

	/*
	* check pagerange input to match valid format, e.g. 1,3,5-7
	* @param e - JQuery element 
	* @return truthy|false
	*/
	function checkInput(value, documentAmount){
		var input = value;
		// var documentAmount = transactionModel.getTransactionData().amount;

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
		restrict: 'A',
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {
			ctrl.$validators.pagerange = function(modelValue, viewValue) {
				if (ctrl.$isEmpty(viewValue) || checkInput(viewValue, attrs.pageAmount)) {
		          // consider empty models to be valid
		          return true;
		        }
		        // it is invalid
		        return false;
			}
		}
	}
});