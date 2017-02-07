"use strict"
var TwoSidedOption = (function(){
	var transactionModel;
	var twoSidedEle;

	function setTwoSided(e) {
		var isTwoSided = $(this).prop('checked');

		transactionModel.setTwoSided( isTwoSided );
	}

	return {

		init: function(transactionObj,ids) {
			transactionModel = transactionObj;
			twoSidedEle = $('#'+ids.twoSidedId).on('click',setTwoSided); 
		},

		updateView: function() {
			//loop through arguments
			for(var i = 0; i < arguments.length; i++) {
				var data = arguments[i];

				//load two sided
				if(data.twoSided == true) {
					twoSidedEle.prop('checked',true);
				} else {
					twoSidedEle.prop('checked',false);
				}
			}
		}

	}
}());