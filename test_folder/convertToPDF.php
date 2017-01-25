<?php

	require_once(__DIR__."/../php/config/Config.php");

	/*
	*
	*/
	function convertFileToPDF($configRefObj,$sourceFilePath,$destinationFolder = null) {
		$newExtension = 'pdf';
		// $softwareName = 'libreoffice';
		$storagePath = $configRefObj->getMethod('getFileStoragePath')->invoke(null); 				//storage path
		// $oFileFullPathName = $this->storagePath.$oFileName;		//original file full path
		// $newFileName = $this->fileName.'.'.$newExtension;		//new file name
		// $newPathFileFullName = $this->storagePath.$newFileName; // new file path full name
		// $spoolPath = $this->spoolPath;							//spool path
		// $maxSpoolTimer = $this->maxSpoolTimer;					//max spool timer



		// session_start();
		// session_write_close();
		// if( $soffice = $_SESSION['SOFFICE'] === true ) { // SOFFICE variable is present, Mac osx only
		// 	$softwareName = '/Applications/LibreOffice.app/Contents/MacOS/' . 'soffice';

		// 	// building command
		// 	$command = sprintf('export HOME=/tmp && %s --headless --convert-to pdf --outdir %s %s',
		// 																		$softwareName,
		// 																		rtrim($storagePath,'/'), //trim traling '/' if any
		// 																		$oFileFullPathName);
		// 	exec($command,$spit,$status);

		// 	if ( in_array('Error',$spit) == true || $status != 0 ) { //An error has occur
		// 	    throw new Exception("An error has occur while converting document. Try uploading a pdf copy instead. $soffice $status"  . implode( ' ', $spit ) );
		// 	} 
		// } else {	//linux system
		// 	//send file to spool for pdf conversion, leveraging rename function to move folder to spool location											
		// 	if( !rename($oFileFullPathName,"$spoolPath$oFileName") ) {
		// 		throw new Exception("Failed to move file to spool directory. Try uploading a pdf copy instead.");
		// 	} 

		// 	$uTime = time(); //create a timer
		// 	$processing = true; 	  //flag to check if still processing
		// 	while ($processing) {
		// 		$response = shell_exec("ls $storagePath | grep -c '$newFileName'"); //check a new file is created at destination
				
		// 		if($response === null) {						//unable to execute command

		// 			throw new Exception("Unable to check spool directory, check spool manager presence. In the mean time, try uploading a pdf copy instead.");

		// 		} else if ($response == 1) { 					//file has sucessfully been converted

		// 			$processing = false;						//turn off processing flag

		// 		} else if(time() - $uTime > $maxSpoolTimer) { //check if file has been in spool for too long
		// 			throw new Exception("File conversion is taking too long, check spool manager presence. In the mean time, try uploading a pdf copy instead.");
		// 		}

		// 	}
		// }

		// return $newPathFileFullName;
	}

	convertFileToPDF(new ReflectionClass( 'Config'),'/var/www/html/sceprinterv3/temporary_file_storage/doc5.doc',null);
?>