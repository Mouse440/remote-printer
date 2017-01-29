<?php

/*
* This script is responsible for validating the uploaded file and fetching information
* about the file and the user.
*/ 
require_once(__DIR__.'/../util/show-error.php');
require_once(__DIR__.'/../security/checkLoginV3.php');

require_once(__DIR__.'/../config/Config.php');
require_once(__DIR__.'/../util/FileUtilities.php');
require_once(__DIR__.'/../util/UploadedFile.php');
require_once(__DIR__.'/../util/AllowanceController.php');
// require_once(__DIR__."/../util/PreviewGenerator.php");
require_once(__DIR__."/../util/PreviewManagerStrategy.php");
require_once(__DIR__."/../util/TransactionUtilities.php");

try {

	if ( $_FILES['file']['error'] == 0 ) { 
		global $uploadedFile;
		
		$uploadedFile = new UploadedFile($_FILES['file']);

		$uploadedFile->validateFile(); //validate file against criteria set			
		$uploadedFile->storeFile();	//store file in temporary storage

		/*
		* The following extraneous variables are a result of a strange bug. When setting the results of the functions
		* directly to the session variable. It is somehow erased once this script exit. The bug doesn't happen when
		* the extension is PDF. A theory why this bug happened is dued to the complexity of the inheritance of the
		* the class structure. This theory still needs to be tested to confirm.
		*/
		$amount = $uploadedFile->getPageAmount();  
	
		$filePrefix = $uploadedFile->getFilePrefix();
		$fileOriginalExt = $uploadedFile->getOriginalFileExtension();

		//default preivew options
		$defaultRange = (intval($amount) > 1) ? "1-$amount" : '1';
		$defaultLayout = 'portrait';

		//initialize a preview generator
		$previewManagerStrategy = new PreviewManagerStrategy(
											new ReflectionClass('Config'),
											$filePrefix,
											$fileOriginalExt,
											$amount);
		$fileToPrintFullPath = $previewManagerStrategy->getPreviewLinks(true,$defaultRange,$defaultLayout); //file to print the preview link
		
		// $fileToPrintFullPath = $previewManagerStrategy->getOriginalFilePath(); //get the original file path
		
		// $fileToPrintFullPath = $uploadedFile->getNewFileFullName(); //get the original file path

		if( $_SESSION['CLOUD_PRINT_DEMO_MODE'] == TRUE ) { 	//demo mode
			$allowance = $_SESSION['CLOUD_PRINT_DEMO_MODE_ALLOWANCE'];
		} else {
			AllowanceController::refreshIndividualAllowance($_SESSION['_USER_']["MemberID"], $_SESSION['_USER_']['Role']);  //do a weekly refresh if needed
			$allowance = AllowanceController::getUserAllowance( 
				     $_SESSION['_USER_']['FirstName'], 
				     $_SESSION['_USER_']['LastName'], 
				     $_SESSION['_USER_']["MemberID"], 
				     $_SESSION['_USER_']['Role'] );
		}

		session_start();
    	$_SESSION['PRINT_TRANSACTION']['amount'] = $amount;
    	$_SESSION['PRINT_TRANSACTION']['fileToPrintFullPath'] = $fileToPrintFullPath; //full file name, this name will be the file print
    	$_SESSION['PRINT_TRANSACTION']['filePrefix'] = $filePrefix; //file prefix - without extension
    	$_SESSION['PRINT_TRANSACTION']['fileOriginalExt'] = $fileOriginalExt; //original file extension
    	$_SESSION['PRINT_TRANSACTION']['allowance'] = $allowance;
    	$_SESSION['PRINT_PREVIEW_OPTIONS']['range'] = $defaultRange; //plan to be used in the future
    	$_SESSION['PRINT_PREVIEW_OPTIONS']['layout'] = $defaultLayout; //plan to be used in the future
    	session_write_close();


		/*
		*  ERROR_STATE example available for you to debug 
		*       session_start();
	    *   	$_SESSION['PRINT_TRANSACTION']['amount'] = $uploadedFile->getPageAmount();  
	    *   	$_SESSION['PRINT_TRANSACTION']['fileToPrintFullPath'] = $uploadedFile->getNewFileName(); //store in session
	    *   	session_write_close();
		*/

		

    	//compile the document results
    	$result = [ 
    				// 'fileToPrintFullPath' => $_SESSION['PRINT_TRANSACTION']['fileToPrintFullPath'],
    				'amount' => $_SESSION['PRINT_TRANSACTION']['amount'],
    				'allowance' => $_SESSION['PRINT_TRANSACTION']['allowance'],
    				'previewLinks' => $fileToPrintFullPath 
    			];

    	echo json_encode($result);
	} else {
		
		//file transfer error
		throw new Exception( "Upload error code: " . $_FILES['file']['error'] ); 
	}

} catch (Exception $e) { //error detected 

	//clearing transaction
	session_start();
	// $_SESSION['PRINT_TRANSACTION'] = null; //store in session
	// unset($_SESSION['PRINT_TRANSACTION']);
	session_write_close();
	if( $uploadedFile !== null) {
		TransactionUtilities::closeTransaction( $uploadedFile->getFilePrefix() );
	}

	echo json_encode( ['error' => $e->getMessage()] ); //'fileCmd' => getFileType($_FILES['file']['tmp_name'])]
}
?>