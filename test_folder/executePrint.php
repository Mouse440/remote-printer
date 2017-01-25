<?php

function executeOrder($command) {
      
      $command .= " | awk '{print $4}'";

      //exec('lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-3 ../lock/PrintingStationProjectDescription.pdf',$es , $r);
      exec($command, $printResponse, $printStatus); 

      if($printStatus === 0) {
      	//get job id from response
      	$positionOfDash = strrpos($printResponse[0],'-');
      	$jobId = substr($printResponse[0], $positionOfDash+1 );
      	return $jobId;
         // $jobId = substr($response[0], strrpos($response[0], '-') + 1); 
      } else {
         // $this->printErrorMsg = implode(' ',$printFeedback);
      	return false
      }
}

executeOrder("lp testdocs/demo.pdf");


?>