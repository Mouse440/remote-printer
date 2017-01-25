<?php
	/*
	* clear transaction from session and que, also return allowance back to user
	**/
	require_once(__DIR__.'/../security/checkLoginV3.php');
	require_once(__DIR__."/../util/show-error.php");
	require_once(__DIR__."/../config/Config.php");
	require_once(__DIR__."/../phase2/RecordController.php");
	require_once(__DIR__."/../util/AllowanceController.php");
	require_once(__DIR__."/../util/TransactionUtilities.php");

	session_start();
	$jobId = $_SESSION['PRINT_TRANSACTION']['jobId'];
	$memId = $_SESSION['_USER_']["MemberID"];

	exec('lprm $jobId 2>&1',$output,$e);


	// $output = array('asdasd'); //debug


	if(empty($output)) { //remove was successful
		//restore allowance
		$restoredAllowance = RecordController::fetchRestoredAllowance($jobId);
		AllowanceController::updateAllowance($restoredAllowance,$memId);

		//update status in record
		RecordController::changePrintRecordStatus('system_canceled',$jobId);
	} else {
		echo "No job id found in queue. Assuming that print job has been sent to printer.";
	}

	//clear transaction
	TransactionUtilities::closeTransaction();
?>