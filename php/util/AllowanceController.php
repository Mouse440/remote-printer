<?php
/*
* This class contains functions to control allowance
*/
require_once(__DIR__ . '/../config/Config.php');

session_start();
session_write_close();


class AllowanceController {

	// private static $RECURSION_LOCKED = FALSE;

	/*
	* Get user allowance
	* @param {String} fn - firstname
	* @param {String} ln - lastname
	* @param {String} MID - member ID
	* @param {String} role - member role 

	* @return string of user allowance
	*/
	public static function getUserAllowance($fn,$ln,$MID,$role = null) {
	       	      
	       /*if($role === null) {
	       	 $role = self::_getWPRole($MID); 	
	       }*/
		//get user role
		//$role = self::_getRole($MID);
		
		//check for membership
		if( $role === false ) { 
			throw new Exception("No role was found. $MID");
		} else { 														
			$allowance = self::_getAllowance($MID);

			if( $allowance === false ) {  //user is not in the print_allowance db

				//create new allowance
				self::_createNewAllowance($fn, $ln, $MID, $role);
				
				//return new allowance
				return self::_getAllowance($MID);

			} else { //user is a member and is on the print_allowance db
				return $allowance; 
			}
		}
	}

	/*
	* Get user allowance 
	* @param MID - Member ID
	* @result - false or int value of allowance
	*/	
	private static function _getAllowance($MID) {
		$con = Config::getPDO();

		//prepare statement
		$stmt = $con->prepare("SELECT `Allowance` FROM `PRINTING`.`print_allowance` WHERE MemID=?");
			
		//execute statement
		$stmt->execute(array($MID));

		//fetch data
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//close stmt
		$stmt = null;

		//no entry found
		if(!$row) { 
			return false;
		}
		return $row['Allowance'];
	}
	
	/*
	* Get wordpress user role
	* @param {String} MID - member id
	* @return {Bool|String} user role name
	* @DEPRECATED
	*/
	private static function _getWPRole($MID) {
		if($user = get_userdata($MID)){
			return $user->roles[0]; 
		}
		return false;
	}

	/*
	* Get user role
	* @param MID - Member ID
	* @return - false or string value of role
	* @DEPRECATED 
	*/
	private static function _getRole($MID) {
		$pdo = Config::getPDO();

		//prepare statement 
		$stmt = $pdo->prepare('SELECT `Role` FROM `SCE-CORE`.`Members` WHERE MemberID=?');

		//execute statement
		$stmt->execute(array($MID));

		//fetch data
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//close stmt
		$stmt = null;

		//no entry found
		if(!$row) { 
			return false;
		}
		return $row['Role'];
	}

	/*
	* Add user to print db and set allowance
	* @param fn - first name
	* @param ln - last name
	* @param MID - member ID
	* @param role - member role
	*/
	private static function _createNewAllowance($fn, $ln, $MID, $role) {
		$pdo = Config::getPDO();
		
		if( in_array( $role , array_keys(Config::$role_reset_amount) )) {
		    $allowanceValue = Config::$role_reset_amount[$role];        
		} else {
		    $allowanceValue = Config::$role_reset_amount['subscriber']; //default value	 	       
		}
		
		//determine allowance value
//		$allowanceValue = ( $role == 'Officer' ) ? 1000 : 30;

		//prepare statement
		$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`print_allowance` (`MemID`,`FirstName`,`LastName`,`Allowance`) VALUES (:MID,:FN,:LN,:ALLOWANCE)');

		//bind parameters
		$stmt->bindParam(':MID',$MID);
		$stmt->bindParam(':FN',$fn);
		$stmt->bindParam(':LN',$ln);
		$stmt->bindParam(':ALLOWANCE',$allowanceValue);

		//execute 
		if( !$stmt->execute() ){
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
			
		}

		//close statement
		$stmt = null;
	}

	/*
   	* update allowance for this user
   	* @param allowance - allowance value to update
   	* @param memID - member id
   	*/
   	public static function updateAllowance($allowance,$memID = false) {
   		if( $allowance == false ) {
			throw new Exception( "Invalid allowance value." );
   		}
   		if( $memID == false ) {
   			throw new Exception( "Invalid member ID: $memID" );
   		} 

 		$pdo = Config::getPDO();

 		$stmt = $pdo->prepare('UPDATE `PRINTING`.`print_allowance` SET `Allowance` = ? WHERE `MemID` = ?');

 		//execute 
		if( !$stmt->execute( array($allowance,$memID) ) ){
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
		}

 		//Update pages left
	    $stmt = null;
   	}

    /*
     * Refresh allowance if past the due date
     * NOTE: This function is atomic and race condition safe
    */
    public static function refreshAllowance(){
       	date_default_timezone_set('America/Los_Angeles'); //set default timezone

    	// if(self::$RECURSION_LOCKED === TRUE) {
    	// 	throw new Exception('Infitinite recursion detected when trying to refresh allowance.');
    	// } else {
    	// 	self::$RECURSION_LOCKED = TRUE;
    	// }
	
	//check if allowance refresh is needed    	
    	$pdo = Config::getPDO();
    	$pdo->beginTransaction();

    	$stmt = $pdo->prepare('SELECT UNIX_TIMESTAMP(`Stamp`) FROM `PRINTING`.`allowance_refresh_stamp` WHERE UID=1 FOR UPDATE');
    	if(!$stmt->execute()) {
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
		}

		//fetch data
		$row = $stmt->fetch(PDO::FETCH_NUM);

		//no entry found
		if(!$row) { 
			//Initialize a record entry
			$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`allowance_refresh_stamp` (`UID`,`Stamp`) VALUES (1,now())');
    		if(!$stmt->execute()) {
				throw new Exception(implode(" ", $stmt->errorInfo() ) );
    		}
			//close stmt
    		$stmt = null;

    		//update allowance (free first time benefit)
    		self::_executeReset($pdo);
    		
    		$pdo->commit();

    		self::refreshAllowance();
		} else {
			//Check if allowance reset was needed 
			if( intval(strtotime("last " . Config::$reset_date)) > intval($row[0]) ) { //compare record unix time with last reset_date unix time
				//update allowance
				self::_executeReset($pdo);

				//update record
				$stmt = $pdo->prepare('UPDATE `PRINTING`.`allowance_refresh_stamp` SET `Stamp`=(now()) WHERE `UID`=1');
	    		if(!$stmt->execute()){
					throw new Exception(implode(" ", $stmt->errorInfo() ) );
	    		}
			}	
    		// self::$RECURSION_LOCKED = FALSE;

			//close stmt
    		$stmt = null;
    		$pdo->commit();
		}
   }


   /*
    * Refresh allowance if past the due date for one individual user
    * @param {String} id - member id
    * @param {String} role - user role
    */
    public static function refreshIndividualAllowance($id,$role){
       	date_default_timezone_set('America/Los_Angeles'); //set default timezone
    	
	//check if allowance refresh is needed    	
    	$pdo = Config::getPDO();
    	$pdo->beginTransaction();

    	$stmt = $pdo->prepare('SELECT UNIX_TIMESTAMP(`Stamp`) FROM `PRINTING`.`allowance_refresh_stamp` WHERE UID=? FOR UPDATE');
    	if( !$stmt->execute(array($id)) ) {
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
		}

		//fetch data
		$row = $stmt->fetch(PDO::FETCH_NUM);

		//no entry found
		if(!$row) { 
			//Initialize a record entry
			$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`allowance_refresh_stamp` (`UID`,`Stamp`) VALUES (?,now())');
    		if(!$stmt->execute(array($id))) {
				throw new Exception(implode(" ", $stmt->errorInfo() ) );
    		}
			//close stmt
    		$stmt = null;

    		//update allowance (free first time benefit)
		self::_resetIndividualAllowance($pdo,$id,$role);  		

    		$pdo->commit();

		} else {
			//Check if allowance reset was needed 
			if( intval(strtotime("last " . Config::$reset_date)) > intval($row[0]) ) { //compare record unix time with last reset_date unix time
				//update allowance
				self::_resetIndividualAllowance($pdo,$id,$role);

				//update record
				$stmt = $pdo->prepare('UPDATE `PRINTING`.`allowance_refresh_stamp` SET `Stamp`=(now()) WHERE `UID`=?');
	    			if(!$stmt->execute(array($id))){
					throw new Exception(implode(" ", $stmt->errorInfo() ) );
	    			}
			}	
		//close stmt
    		$stmt = null;
    		$pdo->commit();
		}
   }
   
	/*
   	* @param $pdo - pdo 
   	* @param MemID - member id 
	* @param $role {String} role - member role
	* 
	*/
	private static function _resetIndividualAllowance($pdo, $MemID, $role){
		$query = "UPDATE `PRINTING`.`print_allowance` SET `Allowance`=? WHERE `MemID`=?";
		$stmt = $pdo->prepare($query);
		
		//get allowance amount
		if( in_array( $role , array_keys(Config::$role_reset_amount) )) {
                    $allowanceValue = Config::$role_reset_amount[$role];
                } else {
                    $allowanceValue = Config::$role_reset_amount['subscriber']; //default value
                }

	  	/* Execute statement */
	        if( !$stmt->execute( array($allowanceValue, $MemID) ) ) {
	            throw new Exception(implode(" ", $stmt->errorInfo() ) );
	        }
	        $stmt = null;
	}


   	/*
   	* retrieve officer list
   	* @param $pdo - pdo
	* @DEPRECATED 
   	*/
	private static function _retrieveOfficerList($pdo) {
		$query = "SELECT `MemberID` FROM `SCE-CORE`.`Members` WHERE `Role`='Officer' or `Role`='Administrator'";
		$stmt = $pdo->prepare($query);

		if( !$stmt->execute() ){
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
		}
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		// $data = $stmt->fetchAll();
	    // while( $stmt->fetch() ) {
	    // 	array_push( $result, $out_memID );
	    // }

	    $stmt = null;
	    return $data;
	}
	
	//reset regular members
	/*
   	* @param $pdo - pdo 
	* @DEPRECATED
	*/
	private static function _resetRegularMembers($pdo) {
		$query = "UPDATE `PRINTING`.`print_allowance` SET `Allowance`=?";
		$stmt = $pdo->prepare($query);
		
	  	 /* Execute statement */
	    if( !$stmt->execute( array(Config::$reset_amount['regularMember']) ) ) {
			throw new Exception(implode(" ", $stmt->errorInfo() ) );
	    }
	    
	    $stmt = null;
	}

	//reset officers
	/*
   	* @param $pdo - pdo 
   	* @param MemID - member id 
	* @DEPRECATED
	*/
	private static function _resetOfficer($pdo, $MemID){
		$query = "UPDATE `PRINTING`.`print_allowance` SET `Allowance`=? WHERE `MemID`=?";
		$stmt = $pdo->prepare($query);
		
	  	 /* Execute statement */
	    if( !$stmt->execute( array(Config::$reset_amount['officer'],$MemID) ) ) {
	        throw new Exception(implode(" ", $stmt->errorInfo() ) );
	    }
	    $stmt = null;
	}

	/*
	* Main function that will execute the reset
   	* @param $pdo - pdo 
	* @DEPRECATED
	*/
	private static function _executeReset($pdo){

		$officerMemIDs = self::_retrieveOfficerList($pdo);

		// print_r($officerMemIDs);
		self::_resetRegularMembers($pdo); 						//reseting regular members id first
		foreach($officerMemIDs as $OfficerID) {					//update officers to higher allowance
			self::_resetOfficer($pdo, $OfficerID['MemberID']);
		}

		return true;
	}
}

?>