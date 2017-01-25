<?php
/*
* This class contains functions for evaluating a function
*/
class FileUtilities {
	private $fileArr;
	private $fileExt;
	private $newFileName;
    public static $tempFileStoragePath = '../temporary_file_storage/';
	
	/*
	* Constructor 
	* @param fileArr - $_FILES
	*/
	public function __construct($fileArr) {
		$this->fileArr = $fileArr;
		$this->fileExt = $this->getFileType(); 
		$this->newFileName = $this->createNewFileName();
		// self::$tempFileStoragePath = 
	}

	/*
	* Get the raw extension from name
	* @return string:raw extension
	* Note: This function is not a reliable method for detectin real extension,
	* it should only be used in complement with other methods
	*/
	private function getRawExtension() {
		$startPos = strrpos($fileArr['file']['name'], '.');
		$rawExt = substr( $fileArr['file']['name'], $startPos ); //find extension of file name
		return strtolower($rawExt);
	}

	/*
	* Check if file has docx extension
	* @return true/false
	*/
	private function isDocxExt($assumedExt) {
		if($this->getRawExtension() === 'docx' && $assumedExt == 'zip') {
			return 'docx';
		} else {
			return false;
		}
	}

	/*
	* @return lowercase string name of file type 
	* NOTE: This function can only detect pdf type at the moment
	*/
	private function getFileType() {
		//Retrieving file extension from mime type
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($this->fileArr['file']['tmp_name']); //get mime type
		$ext = array_search( $mimeType, Config::$extensions, true ); //get extension

		if (false === $ext) {
	        return false;
	    } elseif ( false !== $newExt = $this->isDocxExt( $ext ) ) { //check for "docx" extension
	    	return $newExt;
	    }
	    
    	return $ext;
		// exec ('file ' . $this->fileArr['file']['tmp_name'] . '| awk \'{print $2}\' ', $output);
		// return strtolower( $output[0] );
	}

	/*
	* Create a new hashed file name 
	* @return string : hashed name of file and extension
	*/
	private function createNewFileName() {
		return sha1_file($this->fileArr['file']['tmp_name']);
	}	

	/*
	* Convert file to pdf.
	* Note: this method does not guarantee the integrity of the document.
	* It should only be used to help check document page amount.
	* @return string:spitLocation - the new location of file + file_name.pdf
	* @exception - An error has occur while converting document. Try uploading a pdf copy instead.
	*/
	private function convertFileToPDF() {
		$softwareName = 'libreoffice';
		$spitLocation = self::$tempFileStoragePath; // 
		$fileTempName = $this->fileArr['file']['tmp_name'];

		session_start();
		session_write_close();
		if( $_SESSION['SOFFICE'] == true ) { // SOFFICE variable is present, should be in dev mode when is present
			$softwareName = '/Applications/LibreOffice.app/Contents/MacOS/' . 'soffice';
		} 

		//building command
		$command = sprintf('export HOME=/tmp && %s --headless --convert-to pdf --outdir %s %s',$softwareName,$spitLocation,$fileTempName);
		// $command = sprintf('export HOME=/tmp && %s --headless --convert-to pdf %s',$softwareName,$fileTempName);
		
		exec($command,$spit,$status);

		if (in_array('Error',$spit) == true || $status != 0) { //An error has occur
		    throw new Exception('An error has occur while converting document. Try uploading a pdf copy instead. ' . $command . ' ' . gettype( constant('SOFFICE')) ) ;
		} 

		return $spitLocation . $this->newFileName . '.pdf';
	}

	/*
	* Get the page amount of a ".pdf" document
	* @return page amount
	* @exception Cannot determine page amount!
	*/
	private function getPDFPageAmt($fileTempName){
		//Retrieving number of pages in this file using "xpdf"
	    exec ('pdfinfo ' . $fileTempName . ' | awk \'/Pages/ {print $2}\'', $xpdfOutput, $r);

		if( count($xpdfOutput) > 0 ) {
			$lastIndex = count($xpdfOutput) - 1;				//sometimes $xpdfOutput will yield [0]->'Pages' [1]->Page #
			return $xpdfOutput[ $lastIndex ]; 					
			//throw new Exception('Cannot determine page amount!');
		} else { //no page amount found
			throw new Exception('Cannot determine page amount!');
		}
	}

	/*
	* Get the page amount of a ".doc" document
	*/
	private function getDocPageAmt($fileTempName) {
		$command = "file $fileTempName";

		exec($command,$arr,$r);
		if( $r == 0) {
			$pattern = '/Number of Pages: \d+/';
			preg_match($pattern, $arr[0], $matches);
			$numOfPagesArr = explode(':',$matches[0]);
			$pageAmount = trim( $numOfPagesArr[1] );
			return $pageAmount;
		} else {
			throw new Exception( 'Cannot determine .doc page amount. Try uploading a pdf copy. ' . implode(' ',$arr) . " $r " . $fileTempName );
		}
	}

	/*
	* Get new file name : accessor
	* @return string : new file name + extension
	*/
	public function getNewFileName() {
		return $this->newFileName .'.'. $this->fileExt;
	}

	/*
	* This function will validate file based on set criterias
	*/
	public function validateFile() {
		//check file size
    	if ($this->fileArr['file']['size'] > 10000000) {
    	 	throw new Exception("Unacceptable file size!");
    	} 

    	//check for appropriate extension
    	// if( !in_array( strtolower(substr( $this->fileArr['file']['name'] , strrpos($this->fileArr['file']['name'], '.') + 1) ) , Config::$extensions) ) {
    	// 	throw new Exception( 'Invalid file extension found!' );	
    	// }

    	//check file type
    	if( !$this->fileExt ) {
    		throw new Exception( 'Invalid file type found! Try uploading a pdf copy.' );
    	}
	}

	/*
	* @return string number of pages 
	* @exception thrown when no pages amount if found
	*/
	public function getPageAmount() {
		$unlinkNeeded = false;
		$fileTempName = $this->fileArr['file']['tmp_name']; // default case where file is pdf

		if( $this->fileExt === 'doc'){						//file is .doc

			return $this->getDocPageAmt($fileTempName);

		} elseif( $this->fileExt !== 'pdf') { //check if file conversion is needed, all other type of files 
			$unlinkNeeded = true;
			$fileTempName = $this->convertFileToPDF(); //call conversion method
		}

	    $pageAmt = $this->getPDFPageAmt($fileTempName);

	    //unlink file if unlinkNeeded
	    if( $unlinkNeeded === true ) { //unlink extraneous pdf copy
	    	self::unlinkFile($fileTempName); 
	    }

	    return $pageAmt;
	}

	/*
	* !!!!! DEPRECATED !!!!!
	* Create a signature using crypt() function 
	* @param path to the file
	* @return text of the hashed key
	*/
	public function getSignature() {
		//$rawKey = $PATH;
		$rawKey = null;
		exec ('pdfinfo ' . $this->fileArr['file']['tmp_name'], $xpdfOutput);
		foreach($xpdfOutput as $string) {
			$rawKey .= $string;
		}
		foreach ($xpdfOutput as $string) {
			//grabbing index
			preg_match('/(.+?(?=:))/',$string, $index);

			//grabbing raw content
			preg_match('/:(.+)/', $string, $rawContent);

			//trimming colon and whitespace
			$cleanContent = trim( substr($rawContent[0],1) );

			switch($index[0]) {
				case 'CreationDate':
					$rawKey .= 'CreationDate' . $cleanContent;
					break;
				case 'Pages':
					$rawKey .= 'Pages' . $cleanContent;
					break;
				case 'Page size':
					preg_match('/(.+)(?= pts)/', $cleanContent, $res);
					$rawKey .= 'Page_size' . $res[0];
					break;
				case 'File size':
					preg_match('/\d+/', $cleanContent,$res);
					$rawKey .= 'File_size' . $res[0];
					break;
			}
		}
	
		return crypt($rawKey, Config::$encryption_key); 
		//return $rawKey;
	}
	
   /*
   * Store file in storage directory
   * Note: Inorder for file saving to work, the file to upload to must have the right permission -6- and group www-data (_www for mac)
   * Hint: chmod 765 temporary_file_storage/ & chown www-data:www-data temporary_file_storage/
   */
    public function storeFile() {
      // $fName = addslashes( $this->fileArr["file"]['name'] );
      $newFilePath = self::$tempFileStoragePath . $this->newFileName . '.' . $this->fileExt; 

      //store file in file_log directory with orignal name character escaped
      if(!file_exists( $newFilePath ) ) {
	      if( is_uploaded_file( $this->fileArr["file"]["tmp_name"]) ) {
	         if( move_uploaded_file( $this->fileArr["file"]["tmp_name"], $newFilePath ) ){ //moving file to upload folder $newFilePath
	         	$this->fileArr["file"]["tmp_name"] = $newFilePath; //assign new file path
	         } else {
	            throw new Exception("Unable to save file. ");
	         }
	      } else {
	      		throw new Exception("File did not upload.");
	      } 
      } else { //debug
      		throw new Exception("File already existed.");
      }
    }

    /*
    * Clear file from upload directory
    * return true or false
    * Note: Since this function is static, the path supplied in parameter will be relative to this (FileUtilities) script location
    */
    public static function unlinkFile($filePathRelativeToThisScript) {
      return unlink( $filePathRelativeToThisScript ); //unlink file from upload directory
    }
}

?>