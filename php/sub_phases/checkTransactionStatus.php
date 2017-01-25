<?php
	
	/* NOT USED IN GCP VERSION*/
	require_once(__DIR__."/../util/show-error.php");
	require_once(__DIR__.'/../security/checkLoginV3.php');
	require_once(__DIR__."/../config/Config.php");
	require_once(__DIR__."/../phase2/RecordController.php");
	require_once(__DIR__."/../util/AllowanceController.php");
	require_once(__DIR__."/../util/TransactionUtilities.php");
	require_once(__DIR__."/../job_state_reporter/JobStateReporter.php");

	session_start();
	$jobId = $_SESSION['PRINT_TRANSACTION']['jobId'];
	$memId = $_SESSION['_USER_']["MemberID"];

	$states = Config::$print_states;

	$status = intval( JobStateReporter::report($jobId) );
	// $status = 9; //debug

	$result = Array("status" => $status);

	if($status == 5 || $status == 6) {//canceled or aborted

		//restore allowance
		$restoredAllowance = RecordController::fetchRestoredAllowance($jobId);
		AllowanceController::updateAllowance($restoredAllowance,$memId);

		//update status in record
		RecordController::changePrintRecordStatus($states[$status],$jobId);
		//clear transaction
		TransactionUtilities::closeTransaction();
	} else if($status == 2 || $status == 4 || $status == 7) { //held or stopped or completed
		//update status 
		RecordController::changePrintRecordStatus($states[$status],$jobId);
		//clear transaction
		TransactionUtilities::closeTransaction();
	} else if($status == 1 || $status == 3) { //pending or processing
		//do nothing
	} else {		//something unknown happened
		//restore allowance
		$restoredAllowance = RecordController::fetchRestoredAllowance($jobId);
		AllowanceController::updateAllowance($restoredAllowance,$memId);

		//update status in record
		RecordController::changePrintRecordStatus('unknown',$jobId);
		//clear transaction
		TransactionUtilities::closeTransaction();

		$result["status"] = "unknown $status";
	}

	echo json_encode($result);
?>