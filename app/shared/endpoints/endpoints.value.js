// var appValue = angular.module('appValue',[]);

PrinterApp.valueModule.value('Endpoints', {
	upload: 'php/phase1/validateAndFetch.php',
	fetchPreviewLink: 'php/sub_phases/getPreviewDocLink.php',
	executePrint: "php/phase2/processor.php",
	clearTransaction: "php/sub_phases/clearTransaction.php"
});