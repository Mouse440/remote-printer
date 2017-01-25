"use strict"
var CopiesPanel = (function(){
	
	var transactionModel;
	var copiesInputEle;
	var incrementBtn;
	var decrementBtn;

	function bindCopyInputEvent(copiesEle){
		copiesEle.on("keypress", function(e){
			var keyCode = e.which;
	   		var character = String.fromCharCode(keyCode);
	   		var regex = /[0-9]/;
	   		if( !regex.test(character) ) {
	   			event.preventDefault();
   			}
   			//console.log("triggered");
		}).typing({
			stop: function(evnt, ele) {
				setCopiesValue();
			},
		 	delay: 200
		}).on('blur',function(){ //fill in 1 if empty when blur
			if($(this).val() === "") {
				$(this).val(1);
			}
		});

		return copiesEle;
	}
	
	function setCopiesValue() {
		var copiesVal = copiesInputEle.val();

		if(copiesVal === '') {
			transactionModel.setCopies(0);
		} else {
			transactionModel.setCopies(parseInt(copiesVal) );
		}
	}

	function incrementCopies() {
		//var incrVal = ($(this).prop("value") == "true") ? 1 : -1;
		var incrVal = 1;
		var newCopyAmount = parseInt( copiesInputEle.prop("value") ) + incrVal; 
		if( newCopyAmount <= 999 && newCopyAmount > 0 ){
			copiesInputEle.val(newCopyAmount);
			setCopiesValue();
		}
	}

	function decrementCopies() {
		//var incrVal = ($(this).prop("value") == "true") ? 1 : -1;
		var incrVal = -1;
		var newCopyAmount = parseInt( copiesInputEle.prop("value") ) + incrVal; 
		if( newCopyAmount <= 999 && newCopyAmount > 0 ){
			copiesInputEle.val(newCopyAmount);
			setCopiesValue();
		}
	}

	return {

		init: function(transactionObj,ids) {

			transactionModel = transactionObj;
			copiesInputEle = bindCopyInputEvent( $("#"+ids.copiesInputId) );
			incrementBtn = $("#"+ids.incBtnId).on('click',incrementCopies);
			decrementBtn = $("#"+ids.decBtnId).on('click',decrementCopies);

		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//reset copies values
				if(data.copies === null || data.copies === undefined) {
					//reset page option
					copiesInputEle.text('1'); 	
				} else {
					copiesInputEle.text(data.copies);
				}
			}
		}
	}
}());