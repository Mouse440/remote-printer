<?php
/*
* This class contains functions for accessing/mutating database records
*/
require_once(__DIR__.'/../config/Config.php');
session_start();
session_write_close();	

class RecordController {
	/*
	* Storing error transaction
	* this function will log the print order to db
	* @param errorMsg - error message in string format
	* @param lpCommand - print command (Optional)
	*/
	public static function storeError($errorMsg, $lpCommand) {
		//create pdo object
		$pdo = Config::getPDO();	

		//prepare statement
		$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`print_error` 
							(`MemID`,`ErrorMessage`,`Command`,`Range`,`Copies`,`TwoSided`,`Layout`,`Total`,`Allowance`,`Title`)
								VALUES (:MID,:ErrM,:Cmd,:Rng,:Cp,:TSided,:LO,:Tot,:Alw,:Ttl)');
		
		//bind parameters
		$stmt->bindParam(':MID',$_SESSION['_USER_']['MemberID']);
		$stmt->bindParam(':ErrM',$errorMsg);
		$stmt->bindParam(':Cmd',$lpCommand);
		$stmt->bindParam(':Rng',$_POST['range']);
		$stmt->bindParam(':Cp',$_POST['copies']);
		$stmt->bindParam(':TSided', strVal($_POST['twoSided']) );
		$stmt->bindParam(':LO',$_POST['layout']);
		$stmt->bindParam(':Tot',$_POST['total']);
		$stmt->bindParam(':Alw',$_POST['allowance']);
		$stmt->bindParam(':Ttl', addslashes( $_FILES["file"]['name'] ) );

		//execute 
		if( !$stmt->execute() ){
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
			
		}

		//close statement
		$stmt = null;
	}

	/*
	* Log print to database
	* @param result - print result
	*/
   	public static function logPrint($result) {


   		//create pdo object
	    $pdo = Config::getPDO();
    	$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`print_log` (`SJSUID`,
	                                                `FirstName`,
	                                                `LastName`,
	                                                `JobID`,
	                                                `PagesUsed`,
	                                                `PagesLeft`,
	                                                `PagesAllowed`,
	                                                `PrintCommand`,
	                                                `Version`,
	                                                `Status`) VALUES (:SJSUID,:FNAME,:LNAME,:JOBID,:USED,:LEFT,:ALLOWED,:COMMAND,:VER,:STAT)');
	      

	    $stmt->bindParam(':SJSUID',$result['SJSUID']);
	    $stmt->bindParam(':FNAME',$result['FirstName']);
	    $stmt->bindParam(':LNAME',$result['LastName']);
        $stmt->bindParam(':JOBID',$result['jobId']);
        $stmt->bindParam(':USED',$result['total']);
        $stmt->bindParam(':LEFT',$result['pagesLeft']);
        $stmt->bindParam(':ALLOWED',$result['allowance']);
	    $stmt->bindParam(':COMMAND',$result['command']);
	    $stmt->bindParam(':VER', $v = Config::$version);
	    $stmt->bindParam(':STAT', $st = Config::$print_states[$result['status']] );

	    //execute 
		if( !$stmt->execute() ){
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
		}

	    $stmt = null; //close stmt*/
   	}	

   /*
   * update print record status
   * @param jobId - id of job to fetch
   * @param status - status of print to update
   */
   public static function changePrintRecordStatus($status,$jobId) {
     	//get old allowance
   		$pdo = Config::getPDO();

 		$stmt = $pdo->prepare('UPDATE `PRINTING`.`print_log` SET `Status` = ? WHERE `JobID` = ?');

 		//execute 
		if( !$stmt->execute( array($status,$jobId) ) ){
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
		}

		//Update pages left
	    $stmt = null;
   }
   /*
   * Get the restored value of allowance
   * @param jobId - id of job to fetch
   * @return value of restored allowance
   */
   public static function fetchRestoredAllowance($jobId){
   		$pdo = Config::getPDO();

		//prepare statement 
		$stmt = $pdo->prepare('SELECT `PagesAllowed` FROM `PRINTING`.`print_log` WHERE `JobID`=? ORDER BY `Stamp` DESC LIMIT 1'); //fetch the latest occurence

		//execute statement
		$stmt->execute(array($jobId));

		//fetch data
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//close stmt
		$stmt = null;

		//no entry found
		if(!$row) { 
			return false;
		}

		return $row['PagesAllowed'];
   }

   /*
   * Store feed back
   */
   public static function storeFeedback(){

   		/*
   		* Add new question to database
   		*/
   		function _addNewQuestion(){
   			$pdo = Config::getPDO();

   			$pdo->beginTransaction();

   			$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`feedback_questions` (`Questions`) VALUES(:QUES) ');

		    $stmt->bindParam(':QUES',$_POST['question']);
		    //execute 
			if( !$stmt->execute() ){
				throw new Exception(implode(" ", $stmt->errorInfo() ) );
			}

			$stmt = null;
			$lastInsertedId = $pdo->lastInsertId();
			$pdo->commit();

			return $lastInsertedId;
   		}

   		/*
   		* Add new response to db
   		* @param questionId - question id
   		*/
   		function _addNewResponse($questionId){
   			$pdo = Config::getPDO();

   			$pdo->beginTransaction();

   			$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`feedback` (`SJSUID`,`FirstName`,`LastName`,`QuestionID`,`Answer`) 
   									VALUES(:SJSUID,:FN,:LN,:QID,:ANS) ');

		     $stmt->bindParam(':SJSUID',$_SESSION['_USER_']['SJSUID']);
			 $stmt->bindParam(':FN',$_SESSION['_USER_']['FirstName']);
		     $stmt->bindParam(':LN',$_SESSION['_USER_']['LastName']);
		     $stmt->bindParam(':QID',$questionId);
		     $stmt->bindParam(':ANS',$_POST['answer']);
		     
		     
		    //execute 
			if( !$stmt->execute() ){
				throw new Exception(implode(" ", $stmt->errorInfo() ) );
			}

			$stmt = null;
			$pdo->commit();
   		}

   		$pdo = Config::getPDO();

   		//check if question exist
   		$stmt = $pdo->prepare('SELECT `UID` FROM `PRINTING`.`feedback_questions` WHERE `Questions`=?'); //fetch the latest occurence

		//execute statement
		$stmt->execute(array($_POST['question']));

		//fetch data
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//close stmt
		$stmt = null;

		//no entry found
		if(!$row) { 
			$questionId = _addNewQuestion();
		} else {
			$questionId = $row['UID'];
		}

		_addNewResponse($questionId);
   }
}

?>