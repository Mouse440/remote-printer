<?php
require_once(__DIR__."/../config/Config.php");
/*
* This class contains functions for evaluating a function
*/
class FileUtilities {
    /*
    * Find the interested folders and files and return the path to them
    * @param handle - directory handle
    * @param parentPath - parent path to prefixes
    * @param prefix - the interested prefix
    * @return array - path to the interested files and folders
    */
    public static function getInterestedFilesAndFolders($parentPath,$prefix){
  //   	if ( false == ($handle = opendir($parentPath)) ) {
		// 	throw new Exception("Unable to open directory. Check your path.");
		// }

    	$interested = array(
    					'files' => array(),
    					'folders' => array(),
    					'unknown' => array()
    					);

    	/* Loop over the directory. */
		$scannedDir = array_diff(scandir($parentPath), array('.','..'));  //get all files and folders except . and ..
		
		// while (false !== ($entry = readdir($handle))) {
		foreach($scannedDir as $entry){
			$patt = '/'.$prefix.'/';
			if(preg_match($patt, $entry) === 1) {

				// echo "$patt $entry\n";
			 	$filePath = $parentPath.$entry;
			   	if(is_dir($filePath) == true) {
			   		array_push( $interested['folders'], $filePath );
			   		// $recursiveResult = self::getInterestedFilesAndFolders($filePath,$prefix);
			   		// foreach($recursiveResult as $k => $v){
			   		// 	array_push( $interested[$k], $v );
			   		// }
			   	} elseif( is_file($filePath) == true) {
			   		array_push( $interested['files'], $filePath );
			   	} else {
			   		array_push( $interested['unknown'], $filePath );
			   	}
		   	}
		}
		// }

		// closedir($handle);

		return $interested;
    }

    /*
    * Delete a folder tree
    */
    public static function delTree($dir) { 
	   $files = array_diff(scandir($dir), array('.','..')); 
	    foreach ($files as $file) { 
	      (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file"); 
	    } 
	    return rmdir($dir); 
	} 

    /*
    * Clear file from upload directory
    * return true or false
    * Note: Since this function is static, the path supplied in parameter will be relative to this (FileUtilities) script location
    */
    public static function unlinkFile($filePathRelativeToThisScript) {
        // echo $filePathRelativeToThisScript;
        //unlink file from upload directory
      	if(false == unlink( $filePathRelativeToThisScript )) {
      		//report erro but dont terminate
      		throw new Exception("Couldn't remove file " . $filePathRelativeToThisScript); //debug only
      	}
    }

    /*
    * Clear all file and directories with specified prefix
    * @param parentPath:string - path directory to look into
    * @param prefix:string - common prefix of files
    * 
    */
    public static function unlinkAllWithPrefix($parentPath,$prefix){
    	$interestedPath = self::getInterestedFilesAndFolders($parentPath,$prefix);
		
		// print_r($interestedPath);
		foreach ($interestedPath['files'] as $k => $v) { //wiping all files
			// echo "Deleting $v\n";
			self::unlinkFile($v);
		}

		foreach ($interestedPath['folders'] as $k => $v) { //wiping folders
			// echo "Deleting $v\n";
			self::delTree($v);
		}
    }

    /*
    * Get the file type 
    * @param extensions - array of all supported extensions
    * @param srcFilePath - full path of the source file
	* @return lowercase string name of file type 
	* NOTE: This function can only detect pdf type at the moment
	*/
    public static function determineFileType($extensions,$srcFilePath){
    	//check file existence
    	if(!file_exists($srcFilePath)) {
    		throw new Exception("No file found. $srcFilePath");
    	}

		//Retrieving file extension from mime type
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($srcFilePath); //get mime type
		$ext = array_search( $mimeType, $extensions, true ); //get extension

		if (false === $ext) {
	        throw new Exception("Cannot find extension. $mimeType $srcFilePath");
	    } 
		// exec ('file ' . $this->fileArr['tmp_name'] . '| awk \'{print $2}\' ', $output);
		// return strtolower( $output[0] );
		return $ext;
    }

	/*
	* Unused in this file
	* Convert file in storage folder to pdf.
	* Note: this method does not guarantee the integrity of the document, meaning the conversion 
	* could modify the original content of the file.
	* @return string:spitLocation - the new location of file + file_name.pdf
	* @exception - An error has occur while converting document. Try uploading a pdf copy instead.
	*/
	public static function convertStorageFileToPDF($configRefObj, $srcFilePrefix, $srcFileExt) {
		$newExtension = 'pdf';
		$softwareName = 'libreoffice';
		$storagePath = $configRefObj->getMethod('getFileStoragePath')->invoke(null); 				//storage path
		$oFileName = "$srcFilePrefix.$srcFileExt";														//original file name
		$oFileFullPathName = $storagePath.$oFileName;		//original file full path
		$newFileName = "$srcFilePrefix.$newExtension";		//new file name
		$newPathFileFullName = $storagePath.$newFileName; // new file path full name
		$spoolPath = $configRefObj->getMethod('getSpoolDirPath')->invoke(null);							//spool path
		$maxSpoolTimer = $configRefObj->getStaticPropertyValue('maxSpoolTimer');					//max spool timer

		session_start();
		session_write_close();
		$soffice = $_SESSION['SOFFICE'];
		if( $soffice === true ) { // SOFFICE variable is present, Mac osx only
			$softwareName = '/Applications/LibreOffice.app/Contents/MacOS/' . 'soffice';

			// building command
			$command = sprintf('export HOME=/tmp && %s --headless --convert-to pdf --outdir %s %s',
																				$softwareName,
																				rtrim($storagePath,'/'), //trim traling '/' if any
																				$oFileFullPathName);
			exec($command,$spit,$status);

			if ( in_array('Error',$spit) == true || $status != 0 ) { //An error has occur
			    throw new Exception("An error has occur while converting document. Try uploading a pdf copy instead. $soffice $status"  . implode( ' ', $spit ) );
			} 
		} else {	//linux system
			//send file to spool for pdf conversion, leveraging rename function to move folder to spool location											
			if( !rename($oFileFullPathName,"$spoolPath$oFileName") ) {
				throw new Exception("Failed to move file to spool directory. Try uploading a pdf copy instead.");
			} 

			$uTime = time(); //create a timer
			$processing = true; 	  //flag to check if still processing
			while ($processing) {
				$response = shell_exec("ls $storagePath | grep -c '$newFileName'"); //check a new file is created at destination
				
				if($response === null) {						//unable to execute command

					throw new Exception("Unable to check spool directory, check spool manager presence. In the mean time, try uploading a pdf copy instead.");

				} else if ($response == 1) { 					//file has sucessfully been converted

					$processing = false;						//turn off processing flag

				} else if(time() - $uTime > $maxSpoolTimer) { //check if file has been in spool for too long
					throw new Exception("File conversion is taking too long, check spool manager presence. In the mean time, try uploading a pdf copy instead.");
				}

			}
		}

		return $newPathFileFullName;
	}
}

?>