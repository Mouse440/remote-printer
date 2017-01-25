/*
* This is the main executing script for index.php
*/
"USE STRICT"
$(document).ready(function() {

	//prevent loading drag and drop files on window, restricting to upload zone only.
	window.addEventListener("dragover",function(e){
	  e = e || event;
	  e.preventDefault();
	},false);
	window.addEventListener("drop",function(e){
	  e = e || event;
	  e.preventDefault();
	},false);


	Transaction.init(); //init the transaction model
	//cancel transaction on unload
	window.addEventListener("beforeunload",function(e){ 
		e.preventDefault();

		Transaction.clearTransaction();
	});

	UploadZone.init( Transaction, { 
		'uploadSpaceId' : 'add_file_space',  //init UploadZone
		'fileInputId' : 'file_input_field',
		'uploadFormId' : 'form'
	});

	UploadedFileDisplay.init( Transaction, {
		'fileNameId' : 'file_name',
		'uploadedFileSpaceId' : 'uploaded_file_space',
		'uploadingGif' : 'ajax-loader1'
	}, 
	{
		'deleteIcon' : 'delete-icon'
	}); 

	UploadErrorDisplay.init( Transaction, {
		'errorDisplayId' : 'local_upload_error_display',
		'errorMsgFieldId' : 'error_msg'
	});

	ContinueButton.init( Transaction, {
		'continueButtonId' : 'continue_btn',
		'targetModalId' : 'myModal'
	});

	PreviewDisplay.init( Transaction, {
		'previewPluginsNames' : 'preview-plugin',
		'previewContainer' : 'preview-pdf',
		'loaderAnimationId' : 'preview-loader-animation' 
	});

	TotalPanel.init( Transaction, {
		'totalId' : 'total'
	});

	AllowancePanel.init( Transaction, {
		'allowanceId' : 'allowance'
	});

	CopiesPanel.init( Transaction, {
		'copiesInputId' : 'copies-amount',
		'incBtnId' : 'add-copy-button',
		'decBtnId' : 'subtract-copy-button'	
	});

	PageRangePanel.init( Transaction, { 
		'radioAllInputId' : 'radio-all',
		'radioOtherInputId' : 'radio-other',
		'pageRangeInputId' : 'page-range',
		'pagesOptions' : 'pages-options',
		'pageRangeErrorId' : 'pages-options-error'
	});

	// LayoutPanel.init( Transaction, {
	// 	'radioPortraitId' : 'radio-portrait',
	// 	'radioLandscapeId' : 'radio-landscape'
	// });

	// TwoSidedOption.init( Transaction, {
	// 	'twoSidedId' : 'two-sided-box'
	// });

	PrintButton.init( Transaction, {
		'printBtnId' : 'printBtn'
	}, TotalPanel);

	PrintStatusPanel.init( Transaction, {
		'jobStatusPanelId' : 'jobStatus',
		'printStatusPanelId' : 'modal-processing-div'
	}, TotalPanel);

	Transaction.addObserver(UploadZone);
	Transaction.addObserver(UploadedFileDisplay);
	Transaction.addObserver(UploadErrorDisplay);
	Transaction.addObserver(ContinueButton);
	Transaction.addObserver(CopiesPanel);
	Transaction.addObserver(PageRangePanel);
	Transaction.addObserver(PreviewDisplay);
	Transaction.addObserver(TotalPanel);
	Transaction.addObserver(AllowancePanel);
	// Transaction.addObserver(LayoutPanel);
	// Transaction.addObserver(TwoSidedOption);
	Transaction.addObserver(PrintButton);
	Transaction.addObserver(PrintStatusPanel);

});