<?php
   define('RESETDATE','Sunday');   //reset day of the week
   define('RECORDPATH','./last_updated_date_DO_NOT_DELETE.json'); //path to the record file
  
   /*
   * Refresh allowance if past the due date
   * NOTE: This function is atomic and race condition safe
   */
   function refreshAllowance(){
         /*
         * check record to see if last date 
         * @return true/false - boolean for determine if record needs an update
         */
         function _updateRecordNeeded(){   
            //return record of last updated date
            $record = json_decode( file_get_contents(RECORDPATH, FILE_USE_INCLUDE_PATH) , true );

            //check time of last Sunday with record in last_update_date file, actually only have to check this line, no need to check prev line
            if(strtotime('last ' . RESETDATE) !== $record['lastUpdated'] ) {
               //echo "need to be updated " . $record['lastUpdated'];
               return true;
            } else {
               //echo "NO UPDATE NEEDED " . $record['lastUpdated'];
               return false;
            }
         }

      $fp = fopen(RECORDPATH, 'r+');                        //open a file handle

      if(_updateRecordNeeded()) {                            //check if an update is needed

         flock($fp, LOCK_EX);                               //optain a lock

         if(_updateRecordNeeded()) {                         //check again 
            $data = json_encode( array('lastUpdated'=>strtotime("last " . RESETDATE)) );   //optain data 
            file_put_contents(RECORDPATH, $data, FILE_USE_INCLUDE_PATH);                  // write a new date record

            //refresh all prints
         } 
         flock($fp, LOCK_UN);                          //unlock file
      } 
   }




   /*
   * lock a file exlusively
   */
   function getLock() {
      $fp = fopen(RECORDPATH, 'r+');
      while(!flock($fp, LOCK_EX)){}
      return true;
   }

   /*
   * realese the lock a file 
   */
   function unlockFile(){
      $fp = fopen(RECORDPATH, 'r+');
      flock($fp, LOCK_UN);
   }





  


/*echo "Start time: " . $_SERVER['REQUEST_TIME'] . " Finish time: " . microtime(true);*/
/*session_start();

$_SESSION['_USER_']['FirstName'] = 'first';
	$_SESSION['_USER_']['LastName'] = 'last';
	$_SESSION['_USER_']["MemberID"] = '2';
	$_SESSION['_USER_']["SJSUID"] = '007978256';

	require_once('../util/connection.php');
*/

     /* $copies = $_POST['copies'];
      $layout = ($_POST['layout'] == 'landscape') ? 'landscape' : 'portrait' ;
      $rangeOption = $_POST['range'];
      $filePath = $_FILES['file']['tmp_name'];
      
      $twoSided = null;
      if($_POST['twoSided']) {
         if($layout === 'portrait') {
            $twoSided = 'two-sided-long-edge';
         } else {
            $twoSided = 'two-sided-short-edge';
         }
      } else {
         $twoSided = 'one-sided';
      }*/

      /*
         Specify printer name " -d printername "
         Multiple copies option -n num-copies 
         Two-side printing -o sides=two-sided-long-edge/two-sided-short-edge
         Landscape option -o landscape
         Page ranges -o page-ranges=1-4,7,9-12
         Add file path
      */
      //$command = "lp PrintingStationProjectDescription.pdf" ; //does not work over network  -d $printerName
      //$command = "lp -n $copies -o sides=$twoSided -o $layout -o page-ranges=$rangeOption $filePath" ;   

      //Saving command for storage later
      //$this->command = $command; //save for mysql storage

      /*exec($command,$re,$r);
      exec('lpstat -o | awk \'END {print $1} \' ', $response,$rs ); //get response
      $jobId = substr($response[0], strrpos($response[0], '-') + 1); //get job id from response
      echo $r;      

      echo " im executed!";*/
   //echo _createAndExecuteOrder();
?>	