<?php 
   //include_once('show-error.php');
   require_once(__DIR__.'/../config/Config.php');
/*
* Class responsibilities are to execute print on the command line
*/
class PrintAdapter{

   private $formData;
   //Declaring variables    
   private $dirPath = '../file_log/';
   private $result;

   /*
   * Constructor
   */
   public function __construct($formData){
      $this->formData = $formData;
      $this->result = array(
                           'jobId' => '',
                           'command' => ''
                           );
   }

   /*
   * Execute print and report back
   * @return true on success otherwise return with an error message
   * @exception - Cannot retrieve a Job ID. Please contact an officer for help.
   */
   public function executePrint() {
      $command = $this->buildCommand(); //get command
      $jobId = $this->executeOrder($command); 

      //$jobId = ''; //debugging

      if( $jobId !== false ) {
         // $printed = $this->getStatus($jobId,1);

         // $this->result['pagesLeft'] = intval( $this->formData['allowance'] ) - intval( $this->formData['total'] ); //store new pages-left value
         $this->result['jobId'] = $jobId; //store job id
         $this->result['command'] = $command;
         // $this->result['total'] = $this->formData['total'];

         return true;
      } else {  //No job ID found
         //$this->result['jobid'] = $jobId;  
         throw new Exception("<h4>Cannot retrieve a Job ID. Please contact an officer for help.<h4> $command");
      }
   }

   /*
   * Create print command
   * @return command:string or false if failed
   */
   private function buildCommand(){
      $copies = $this->formData['copies'];
      $layout = ($this->formData['layout'] == 'landscape') ? 'landscape' : 'portrait' ;
      $rangeOption = $this->formData['range'];
      $filePath = Config::getPrinterAppRoot().'/'.$this->formData['fileToPrintFullPath'];
      
      //throw new Exception("Filepath: $filePath");
      

      //$twoSided = null;
      if($this->formData['twoSided'] === 'true') {
         if($layout === 'portrait') {
            $twoSided = 'two-sided-long-edge';
         } else {
            $twoSided = 'two-sided-short-edge';
         }
      } else {
         $twoSided = 'one-sided';
      }

      /*
         Specify printer name " -d printername "
         Multiple copies option -n num-copies 
         Two-side printing -o sides=two-sided-long-edge/two-sided-short-edge
         Landscape option -o landscape
         Page ranges -o page-ranges=1-4,7,9-12
         Add file path
      */
      //$command = "lpr -P $printerName -#$copies -o sides=$twoSided -o $layout $rangeOption $filePath" ; //does not work over network  -d $printerName
      // $command = "lp -n $copies -o sides=$twoSided -o $layout -o page-ranges=$rangeOption $filePath" ;   
      $command = "lp -n $copies -o sides=$twoSided $filePath" ;   

      return $command; 
   }

   /*
   * Execute print order
   * @return string - jobID or false if failed
   */
   private function executeOrder($command) {
      $command .= " | awk '{print $4}'";

      //exec('lp -n 1 -o sides=two-sided-long-edge -o portrait -o page-ranges=1-3 ../lock/PrintingStationProjectDescription.pdf',$es , $r);
      exec($command, $printResponse, $printStatus); 

      if($printStatus == 0) {
         //get job id from response
         $positionOfDash = strrpos($printResponse[0],'-');
         $jobId = substr($printResponse[0], $positionOfDash+1 );
         return $jobId;
         // $jobId = substr($response[0], strrpos($response[0], '-') + 1); 
      } else {
         throw new Exception("Execute order failed with code $printStatus - " . implode(' ', $printResponse));
         
         // $this->printErrorMsg = implode(' ',$printFeedback);
         return false;
      }
   }

   /*
   * Get the result of the transaction
   * @return Array - result array
   */
   public function getResult(){
      return $this->result;
   }

   /*
   * Determine status of print job
   * @param jobId - job id
   * @param waitTime - time for function to wait for in seconds
   * @return bool - true/false
   * Note: Jobs are removed from queue when all the data have been sent to the device, otherwise it should still be in queue
   */
   public function getStatus($jobId,$waitTime) {
      $completed = false;
      $endTime = time() + $waitTime ; //timer for counting how long is left
      
      while( time() < $endTime ) {
         time_nanosleep( 0 , 100000000 ); //delay 
         exec( "lpq | awk '/ $jobId /{print $1}' " , $res ); //check if job is completed 
         
         //exec( "lpq | awk '/ $jobId /{print $1}'" , $res );
         $this->rank = $res[0]; //store for rank
         if( empty($res[0]) ) {//print has been cleared (sent/removed) from queue
            $completed = true;
            break;
         } else { 
            unset($res); //unset result to prevent concatenation
         }
      }
      return $completed;
   }

   /*
   * Cancel print job
   * @param jobId - jobId to cancel
   */
   public function cancelPrint($jobId) {
      exec("lprm $jobId");
   }

   /*
   * store file in file_log directory
   * Note: Inorder for file saving to work, the file to upload to must have the right permission -6- and group www-data
   */
   public function storeFile() {
      $fName = addslashes( $_FILES["file"]['name'] );
      
      //store file in file_log directory with orignal name character escaped
      if( !file_exists($this->dirPath . $fName) && is_uploaded_file( $_FILES["file"]["tmp_name"]) ) {
         if( !move_uploaded_file( $_FILES["file"]["tmp_name"] , $this->dirPath . $fName ) ){ //moving file to upload folder
            storeError(" Unable to save file. ss");
         } 
      } 
   }

   /*
   * store file information 
   */
   public function storeFileInfo(){
      $name = addslashes( $_FILES["file"]['name'] );

      //Gather information about file 
      if( !file_exists($this->dirPath .$name.'info.txt') ) {
         exec ('pdfinfo ' . $_FILES["file"]["tmp_name"] , $output); //fetch file information 
         foreach($output as $str) {                                  //loop through output and store into a text file in file_log
            file_put_contents($this->dirPath.$name.".info.txt", "$str\n", FILE_APPEND | LOCK_EX ); 
         }
         //file_put_contents("../file_log/example.info.txt", 'concaaa', FILE_APPEND | LOCK_EX );
      } 
   }


   // //Clear file from upload directory
   // public function clearFile() {
   //    $fName = addslashes( $this->formData["fileName"] );
   //    unlink( $this->dirPath . $fName ); //unlink file from upload directory
   // }


}
?>