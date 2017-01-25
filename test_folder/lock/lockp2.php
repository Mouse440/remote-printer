<?php
#
	# create a new pdo variable
	#
	function getPDO(){
		$host = 'localhost'; $dbuser = "Printer"; $dbpass = "@sce123";
		try {
			return new PDO("mysql:host=$host;dbname=PRINTING;charset=utf8", 
								$dbuser, $dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::ATTR_PERSISTENT => true) 
						   );
		} catch(PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
    		exit;
		}
	}

/*
	* Log print to database
	* @param result - print result
	*/
   	function logPrint($result) {


   		//create pdo object
	    $pdo = getPDO();
    	$stmt = $pdo->prepare('INSERT INTO `PRINTING`.`print_log` (`SJSUID`,
	                                                `FirstName`,
	                                                `LastName`,
	                                                `JobID`,
	                                                `PagesUsed`,
	                                                `PagesLeft`,
	                                                `PagesAllowed`,
	                                                `PrintCommand`) VALUES (:SJSUID,:FNAME,:LNAME,:JOBID,:USED,:LEFT,:ALLOWED,:COMMAND)');
	      

	    
	    $stmt->bindParam(':SJSUID',$a = 'test');
	    $stmt->bindParam(':FNAME',$b = 'test');
	    $stmt->bindParam(':LNAME',$c = 'test');
        $stmt->bindParam(':JOBID',$d = 'test');
        $stmt->bindParam(':USED',$s = 2);
        $stmt->bindParam(':LEFT',$g = 3);
        $stmt->bindParam(':ALLOWED',$y = 5);
	    $stmt->bindParam(':COMMAND',$h = 'test');
	      
	    //execute 
		if( !$stmt->execute() ){
			echo 'false';
			throw new Exception($stmt->errorInfo());
		}

	    $stmt = null; //close stmt*/
   	}	

   	logPrint();
	
?>