<?php

/*
* Test driver for checkTransactionStatus.php
*/
class TESTER {

	/*
	* print file
	* @param $f - file path
	* @return jobId
	*/
	public static function executePrint($f) {
		//case 1 - pending
		$rawJobId = shell_exec("lp $f | awk '{print $4}'");
		//get job id from response
		$positionOfDash = strrpos($rawJobId,'-');
		return $jobId = substr($rawJobId, $positionOfDash+1 );
	}
}

session_start();
$file = __DIR__."/../testdocs/doc2.pdf";

//case 1 - pending
$jobId = TESTER::executePrint($file);
$_SESSION['PRINT_TRANSACTION']['jobId'] = $jobId;
echo "Case 1\nPrint status is: ";
include(__DIR__.'/../../php/sub_phases/checkTransactionStatus.php');
echo "\nExpected: 1\n\n";
exec("lprm $jobId");

//case 2 - held
$jobId = intval( TESTER::executePrint($file) );
$_SESSION['PRINT_TRANSACTION']['jobId'] = $jobId;
exec("lp -i $jobId -o job-hold-until=indefinite"); //hold job
echo "Case 2\nPrint status is: ";
include(__DIR__.'/../../php/sub_phases/checkTransactionStatus.php');
echo "\nExpected: 2\n";
exec("lprm $jobId");

?>