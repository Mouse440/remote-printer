<?php
	/*
	* clear the transaction from session and queue
	*/
	// require_once(__DIR__.'/../config/Config.php');
	// require_once(__DIR__.'/../util/FileUtilities.php');
	// session_start();
	// // echo session_id();

	// // print_r($_SESSION);

	// //clear file from storage
	// FileUtilities::unlinkAllWithPrefix(Config::getFileStoragePath(),$_SESSION['PRINT_TRANSACTION']['filePrefix']);
	// // echo Config::getFileStoragePath().$_SESSION['PRINT_TRANSACTION']['hashedFName'];

	// $_SESSION['PRINT_TRANSACTION'] = null; //unset transaction
	// unset($_SESSION['PRINT_TRANSACTION']);
	// session_write_close();
	require_once(__DIR__.'/../security/checkLoginV3.php');
	require_once(__DIR__."/../util/TransactionUtilities.php");
	//clear transaction
	TransactionUtilities::closeTransaction();

?>