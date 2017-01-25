<?php 
	if(constant('DEMO_MODE') == true) {
		session_start();
		$_SESSION['CLOUD_PRINT_DEMO_MODE'] = true;
		$_SESSION['CLOUD_PRINT_DEMO_MODE_ALLOWANCE'] = 10;
		session_write_close();
	} else {
		session_start();
		$_SESSION['CLOUD_PRINT_DEMO_MODE'] = null;
		$_SESSION['CLOUD_PRINT_DEMO_MODE_ALLOWANCE'] = null;
		unset($_SESSION['CLOUD_PRINT_DEMO_MODE']);
		unset($_SESSION['CLOUD_PRINT_DEMO_MODE_ALLOWANCE']);
		session_write_close();
	}