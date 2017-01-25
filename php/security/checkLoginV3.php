<?php

	// include(__DIR__.'/../util/show-error.php');
	/*
		This script checks user credential. 
		It will intelligently detect development mode and will not redirect.
		Note: Be sure to include a dev file that include a user session if you are in development mode.
	*/
	include_once constant('DEMO_FILE_PATH');  //include demo file.

	session_start();
	session_write_close();
	if( constant('OUT_OF_SERVICE') == TRUE) { //send users to out of service page
		header('Location: out_of_service.php');
	}

	if( constant('DEV_MODE') == TRUE) {	//check dev mode
		include_once constant('DEV_FILE_PATH');  //include dev file.
		// print_r($_SESSION);
	} 

	if( !isset($_SESSION['_USER_']) && !isset($_SESSION['CLOUD_PRINT_DEMO_MODE']) ) {    //check for $_SESSION and demo mode false
		header('Location: ../');        //return user to login page
	} 
?>
