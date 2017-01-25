<?php 
	require_once(__DIR__."/../php/job_state_reporter/JobStateReporter.php");

	echo JobStateReporter::report(10);
?>