PrinterApp.servicesModule.factory('OptionsService', ["EndpointRequest", function(EndpointRequest) {
	var vm = {};

	function init() {
		vm.allowance = "";
		vm.pagerange = "";
		vm.pagerangeAmount = "";
		vm.originalPagerange = "";
		vm.page_amount = "";
		vm.copies = 1;
		vm.twoSided = true;
		vm.layout = 'portrait';
		vm.previewLink = "";
		vm.filename = "";

		vm.printInProgress = false;
		vm.printJobDone = false;
		vm.printJobError = "";
	};	
	init();

	vm.total = function() {
		var total = vm.copies * vm.pagerangeAmount || 0;
		return total;
	}

	vm.getRangeAmount = function(range, amount) {
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

	vm.clearTransaction = function() {
		init(); //reinit to clear reset all values
		EndpointRequest.clearFile();
	}

	return vm;
}]);