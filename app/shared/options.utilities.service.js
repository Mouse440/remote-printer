PrinterApp.servicesModule.service('OptionsUtilities', function() {
	this.getRangeAmount = function(range, amount) {
		if( !range ) {
			return 0;
		} else {
			var array = range.split(","); //Turn input ins nodes
			var result = 0;
			for( var i in array ) {
				var node = array[i]; 
				if(/\-/.test(node)) { //test if node is a range of numbers
					var dashIndex = node.indexOf("-");
					var firstNum = parseInt(node.substring(0 , dashIndex));
					var secondNum = parseInt(node.substring(dashIndex+1, node.length));
					secondNum = (secondNum > amount) ? amount : secondNum; //Check for the case when last number is larger than lastpage

					result += secondNum-firstNum+1;
				} else { //node contains only a number
					result += 1;
				}
			}
			return result;
		}
	}
})