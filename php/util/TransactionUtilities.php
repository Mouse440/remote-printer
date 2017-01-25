<?php
require_once(__DIR__.'/../config/Config.php');
require_once(__DIR__.'/FileUtilities.php');
// require_once(__DIR__.'/../job_state_reporter/JobStateReporter.php');

/*
* This class is a utility class for common transaction methods
*/
class TransactionUtilities {

	/*
	* Close transaction
	* @param filePrefix - optional file prefix
	* NOTE: The parameter is optional. If the user specify what the prefix is, it means that the session variable was not set before 
	* a close transaction is needed
	*/
	public static function closeTransaction($filePrefix = NULL) {
		$isManualClose = ($filePrefix !== NULL) ? true : false; //check if this call is manually passing argument
			
		session_start();
		$filePrefix = ($filePrefix === NULL) ? $_SESSION['PRINT_TRANSACTION']['filePrefix'] : $filePrefix; //get the appropriate file prefix

		if( isset($_SESSION['PRINT_TRANSACTION']) || $isManualClose ){
			//clear all files
			FileUtilities::unlinkAllWithPrefix(Config::getFileStoragePath(),$filePrefix);
			FileUtilities::unlinkAllWithPrefix(Config::getSpoolDirPath(),$filePrefix);

			//remove from queue
			$jobId = $_SESSION['PRINT_TRANSACTION']['jobId'];
			shell_exec('lprm $jobId');

			//clear transaction
			$_SESSION['PRINT_TRANSACTION'] = null; //unset transaction
			unset($_SESSION['PRINT_TRANSACTION']);
			
		}
		session_write_close();
	}

	// public static function 

}

?>