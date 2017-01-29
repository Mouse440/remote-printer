<?php
	/*
	*	Processor handle action request and handle print logging
	*	This script returned either a 'status' or 'error'. 
	*	Status is a number from 1 to 7. 
	*                 1 - 'pending'
 	*                 2 - 'held'
 	*                 3 - 'processing'
 	*                 4 - 'stopped'
	*                 5 - 'canceled'
	*                 6 - 'aborted'
	*                 7 - 'completed'
	*   Error message is any exception thrown in the program
	*
	* 	EXAMPLE OF DATA RECEIVED
	*	$_POST Array
	*	(
	*	    [range] => 1-8
	*	    [copies] => 1
	*	    [twoSided] => false
	*	    [layout] => portrait
	*	    [total] => 8
	*	    [amount] => 8
	*	    [allowance] => 10
	*	)
	*	$_FILE Array
	*	(
	*	    [name] => www.cs.unca.edu_~bruce_Fall11_255_PIC24_instruction_set_summary.pdf
	*	    [type] => application/pdf
	*	    [tmp_name] => /tmp/phpdElZZB
	*	    [error] => 0
	*	    [size] => 190881
	*	)
	*/

	session_start();
	session_write_close();
	
	require_once(__DIR__.'/../security/checkLoginV3.php');
	require_once(__DIR__."/PrintAdapter.php");
	require_once(__DIR__."/GCPPrintAdapter.php");
	require_once(__DIR__.'/RecordController.php');
	require_once(__DIR__.'/../util/POController.php');
	require_once(__DIR__."/../util/AllowanceController.php");
	require_once(__DIR__."/../util/FileUtilities.php");
	require_once(__DIR__."/../util/TransactionUtilities.php");
	// require_once(__DIR__."/../job_state_reporter/JobStateReporter.php");
	// require_once(__DIR__."/../util/functions.php");

	try {
		/*
		* Explicit declaration of transaction data, this was done for readability purposes
		*/
		$transactionData =  array(
			'amount' => $_SESSION['PRINT_TRANSACTION']['amount'],
	    	'fileToPrintFullPath' => $_SESSION['PRINT_TRANSACTION']['fileToPrintFullPath'], //full file name 
	    	'filePrefix' => $_SESSION['PRINT_TRANSACTION']['filePrefix'], //file prefix - without extension
	    	'allowance' => $_SESSION['PRINT_TRANSACTION']['allowance'],
	    	'SJSUID' => $_SESSION['_USER_']['SJSUID'],
	    	'FirstName' => $_SESSION['_USER_']['FirstName'],
	    	'LastName' => $_SESSION['_USER_']['LastName'],
	    	'MemberID' => $_SESSION['_USER_']["MemberID"]
		);
		
		//print_r($transactionData);

		//stage 1
		$mergedData = array_merge($_POST,$transactionData);					//merge post data
		
		// throw new Exception("just a test");
		
		POController::validateData($mergedData); 							//validating data

		$pagesLeft = intval( $mergedData['allowance'] ) - intval( $mergedData['total'] );

		//demo mode
		// if( $_SESSION['CLOUD_PRINT_DEMO_MODE'] == TRUE ) { 
        	echo json_encode( array('status' => 7, 'pagesLeft' => $pagesLeft) );								//return result
			exit;
		// }

		/*
		USB Printer Flow
		//initialize PrintAdapter
		$PH = new PrintAdapter($mergedData); 							
		//execute print
		$PH->executePrint();												//print succeeded
		
		//stage 2

        //merge data into result data array
		$resultData = array_merge( $PH->getResult(), $mergedData );

		//calculate pages left
		$resultData['pagesLeft'] = $pagesLeft;
		

			//get status of print job
		$resultData['status'] = JobStateReporter::report($resultData['jobId']);

		if( $resultData['status'] == 7 ){ //check if status is completed
			//update status 
			RecordController::changePrintRecordStatus('completed',$jobId);
			//clear transaction
			TransactionUtilities::closeTransaction();
		} 
		*/

		// Google Cloud Printing Flow
		$PH = new GCPPrintAdapter($mergedData); 							
		//execute print
		$status = $PH->executePrint();

		$resultData = array_merge( $PH->getResult(), $mergedData );

		//calculate pages left
		$resultData['pagesLeft'] = $pagesLeft;

		if( $status === true ){ //check if status is completed
			$resultData['status'] = 7; 
			//update status 
			RecordController::changePrintRecordStatus('completed',$jobId);
			//clear transaction
			TransactionUtilities::closeTransaction();
		} else {
			throw new Exception($status);
		}
		//end of Google Cloud Printing Flow


		//store success time
		session_start();
        $_SESSION['PRINT_TRANSACTION']['stamp'] = microtime(true);
        $_SESSION['PRINT_TRANSACTION']['jobId'] = $resultData['jobId'];
        session_write_close();

		//log print record
		RecordController::logPrint( $resultData ); 							    //log print to db
        
		//update allowance
        AllowanceController::updateAllowance($resultData['pagesLeft'],$resultData['MemberID']);      //update allowance for this user

        //respond with status
        echo json_encode( array('status' => $resultData['status'], 'pagesLeft' => $resultData['pagesLeft']) );								//return result
		// print_r( $_POST);
		// exit;
	} catch (Exception $e) {
		
		//storefile -- need to be implemented

		TransactionUtilities::closeTransaction();
		// print_r($_SESSION);
		echo json_encode(['error' => $e->getMessage()]); 
	}
	

	//check page amount and compare with $posix_times(oid)
	//check allowance amo

	// } else { 												//failed to retrieve jobid

		// 	//clear transaction, remove all files

		// 	RecordController::storeError($result['error'],$result['command']);
		// 	$PH->storeFileInfo();								//store pdfinfo result of the file

		// 	//This line must be last because it affects default file location
		// 	$PH->storeFile(); 								    //store file before asking for permission
		// } 

		

		// switch ($_POST['action']) {
		// 	case 'print':
				

		// 		break;
			// case 'check':
				
			// 	//CHECK jobId for existence
			// 	$report = array();
			// 	//wait for k seconds 
			// 	if($PH->getStatus($_POST['jobId'],4) ){ 				//job is completed
			// 		$report['status'] = 'success';
			// 	} else { 												//job did not complete within k seconds


			// 		//remove the job from queue
			// 		$PH->cancelPrint($_POST['jobId']);

			// 		//restore allowance
			// 		RecordController::changePrintRecordStatus('Canceled',$_POST['jobId']);
			// 		$restoredAllowance = RecordController::fetchRestoredAllowance($_POST['jobId']);
			// 		AllowanceController::updateAllowance($restoredAllowance);

			// 		$report['status'] = 'canceled';
			// 		$report['msg'] = '<h4> Due to an internal problem, your print job is taking too long to process and  has been canceled. '. 
			// 							"Your allowance has been restored to <span style='color:green'>$restoredAllowance</span>. Please contact an officer for help.</h4>";
			// 	}
			// 	echo json_encode($report);
			// 	break;
			// case 'store': 												 //reverse meaning, this case will clear file depending on permission
			// 	if($_POST['permission'] === 'false'){ 					 //has permission to store file 
			// 		$PH->clearFile();									 //clear file that was originally stored in previous request
			// 		//echo json_encode("{'sup':".$_POST['fileName']."}");
			// 	}
			// 	break;
			// case 'feedback': //users feedback
			// 	//check for empty
			// 	if( empty($_POST['answer']) !== true ) {
			// 		//send to database
			// 		RecordController::storeFeedBack();
			// 	}
			// 	break;
			// default:
			//  	//log them out, something fishy is going on
			// 	echo "didnt catch anything";
?>