<?php
	if(constant('DEV_MODE') == true) {
		
		session_start();
		$_SESSION['_USER_'] = array();
		#####DEV ONLY################################
		$_SESSION['_USER_']['FirstName'] = 'Joe';
		$_SESSION['_USER_']['LastName'] = 'Blow';
		$_SESSION['_USER_']["MemberID"] = '2';
		$_SESSION['_USER_']['SJSUID'] = '111111111';
		#############################################
		
		if(constant('SOFFICE_NEEDED') === true) { //SOFFICE is needed
			// DEFINE('SOFFICE','/Applications/LibreOffice.app/Contents/MacOS/');
			$_SESSION['_USER_']['SOFFICE'] = true;
		} else {
			$_SESSION['_USER_']['SOFFICE'] = false;
		}
		
		session_write_close();
	} else {
		echo 'WRONG PLACE TO BE MY FRIEND, THIS PAGE IS CONTAMINATED WITH A VIRUS. GET OUT NOW.....';
	}
?>