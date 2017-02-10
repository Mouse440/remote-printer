<?php
require_once(__DIR__."/../config/Config.php");
// require_once('FileUtilities.php');
require_once('PageAmtHandlingStrategy.php');

/*
* This class is responsible for disecting a file and returning desired information about that file
* 
*/
class UploadedFile {
	private $fileArr;
	private $fileType;
	private $newFileName;
    private $tempFileStoragePath;
    private $spoolDirPath;
	private $pageAmtHandlingStrategy;
	private $newFileFullName;
	// private $previweManagerStrategy;

	/*
	* Constructor 
	* @param fileArr - $_FILES
	*/
	public function __construct($fileArr) {
		$this->fileArr = $fileArr;
		$this->fileType = $this->getFileType(); 
		$this->newFileName = $this->createNewFileName();
		$this->tempFileStoragePath = Config::getFileStoragePath();
		$this->spoolDirPath = Config::getSpoolDirPath();
		$this->pageAmtHandlingStrategy = new PageAmtHandlingStrategy(
											Config::$maxSpoolTimer,
											$this->spoolDirPath,
											$this->tempFileStoragePath,
											$this->newFileName,
											$this->fileType);

	}

	// /*
	// * Generate a stream of 10 random characters
	// * @return string : random characters
	// */
	// private function generateRandomName() {
	// 	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	// 	$key = '';
	// 	for ($i = 0; $i < 10; $i++) {
	// 	     $key .= $characters[rand(0, strlen($characters) - 1)];
	// 	}
	// 	return $key;
	// }

	/*
	* Create a new hashed file name 
	* @return string : hashed name of file and extension
	*/
	private function createNewFileName() {
		return sha1_file($this->fileArr['tmp_name']).'.'.microtime(true);
	}	

	/*
	* @return lowercase string name of file type 
	* NOTE: This function can only detect pdf type at the moment
	*/
	private function getFileType() {
		//Retrieving file extension from mime type
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($this->fileArr['tmp_name']); //get mime type
		$ext = array_search( $mimeType, Config::$extensions, true ); //get extension

		if (false === $ext) {
	        return false;
	    } elseif ( false !== $newExt = $this->isDocxExt( $ext ) ) { //check for "docx" extension
    		return $newExt;
	    }
	    
    	return $ext;
		// exec ('file ' . $this->fileArr['tmp_name'] . '| awk \'{print $2}\' ', $output);
		// return strtolower( $output[0] );
	}

	/*
	* Get the raw extension from name
	* @return string:raw extension
	* Note: This function is not a reliable method for detectin real extension,
	* it should only be used in complement with other methods
	*/
	private function getRawExtension() {
		$startPos = strrpos($this->fileArr['name'], '.');
		$rawExt = substr( $this->fileArr['name'], $startPos+1 ); //find extension of file name
		return strtolower($rawExt);
	}

	/*
	* Check if file has docx extension
	* @return true/false
	* NOTE: A better way to check this is to try to convert file to pdf and see if it fails
	*/
	private function isDocxExt($assumedExt) {
		$rawExt = $this->getRawExtension();
		if( $rawExt === 'docx' && $assumedExt == 'zip') {
			return 'docx';
		} else {
			return false;
		}
	}

	/*
	* Get the page amount of a ".pdf" document
	* @return page amount
	* @exception Cannot determine page amount!
	*/
	// private function getPDFPageAmt($fileTempName){
	// 	//Retrieving number of pages in this file using "xpdf"
	//     exec ('pdfinfo ' . $fileTempName . ' | awk \'/Pages/ {print $2}\'', $xpdfOutput, $r);

	// 	if( count($xpdfOutput) > 0 ) {
	// 		$lastIndex = count($xpdfOutput) - 1;				//sometimes $xpdfOutput will yield [0]->'Pages' [1]->Page #
	// 		return $xpdfOutput[ $lastIndex ]; 					
	// 		//throw new Exception('Cannot determine page amount!');
	// 	} else { //no page amount found
	// 		throw new Exception('Cannot determine page amount!');
	// 	}
	// }

	/*
	* Get the page amount of a ".doc" document
	*/
	// private function getDocPageAmt($fileTempName) {
	// 	$command = "file $fileTempName";

	// 	exec($command,$arr,$r);
	// 	if( $r == 0) {
	// 		$pattern = '/Number of Pages: \d+/';
	// 		preg_match($pattern, $arr[0], $matches);
	// 		$numOfPagesArr = explode(':',$matches[0]);
	// 		$pageAmount = trim( $numOfPagesArr[1] );
	// 		return $pageAmount;
	// 	} else {
	// 		throw new Exception( 'Cannot determine .doc page amount. Try uploading a pdf copy. ' . implode(' ',$arr) . " $r " . $fileTempName );
	// 	}
	// }

	/*
	* Get hashed file prefix
	* @return string: file prefix
	*/
	public function getFilePrefix(){
		return $this->newFileName;
	}

	/*
	* Get file original type, 
	* @return string: extension name, e.g. pdf, png, etc.
	*/
	public function getOriginalFileExtension() {
		return $this->fileType;
	}

	/*
	* This function will validate file based on set criterias
	*/
	public function validateFile() {
		//check file size
    	if ($this->fileArr['size'] > 10000000) {
    	 	throw new Exception("Unacceptable file size!");
    	} 

    	//check for appropriate extension
    	// if( !in_array( strtolower(substr( $this->fileArr['name'] , strrpos($this->fileArr['name'], '.') + 1) ) , Config::$extensions) ) {
    	// 	throw new Exception( 'Invalid file extension found!' );	
    	// }

    	//check file type
    	if( !$this->fileType ) {
    		throw new Exception( 'Invalid file type found! Try uploading a pdf copy.' );
    	}
	}

	/*
	* Get new file name : accessor
	* @return string : new file name + extension
	*/
	public function getNewFileName() {
		// return $this->newFileName .'.'. $this->fileType;
		return $this->pageAmtHandlingStrategy->getNewFileName();
	}

	/*
	* Get new file full name : accessor
	* @return string : new file name + extension
	*/
	public function getNewFileFullName() {
		// return $this->newFileName .'.'. $this->fileType;
		return $this->newFileFullName;
	}

	/*
	* @return string number of pages 
	* @exception thrown when no pages amount if found
	*/
	public function getPageAmount() {
		// $unlinkNeeded = false;
		// $fileTempName = $this->fileArr['tmp_name']; // default case where file is pdf

		// if( $this->fileType === 'doc'){						//file is .doc

		// 	return $this->getDocPageAmt($fileTempName);

		// } elseif( $this->fileType !== 'pdf') { //check if file conversion is needed, all other type of files 
		// 	$unlinkNeeded = true;
		// 	$fileTempName = $this->convertFileToPDF(); //call conversion method
		// }

	 //    $pageAmt = $this->getPDFPageAmt($fileTempName);

	 //    //unlink file if unlinkNeeded
	 //    if( $unlinkNeeded === true ) { //unlink extraneous pdf copy
	 //    	self::unlinkFile($fileTempName); 
	 //    }

	 //    return $pageAmt;
	    return $this->pageAmtHandlingStrategy->getPageAmount();
	}

   /*
   * Store file in storage directory
   * Note: Inorder for file saving to work, the file to upload to must have the right permission -6- and group www-data (_www for mac)
   * Hint: chmod 765 temporary_file_storage/ & chown www-data:www-data temporary_file_storage/
   */
    public function storeFile() {
      // $fName = addslashes( $this->fileArr["file"]['name'] );
      $newFilePath = $this->tempFileStoragePath . $this->newFileName . '.' . $this->fileType; 
      // $newFilePath = '../temporary_file_storage/'. $this->newFileName.'.'.$this->fileType; 
      $this->newFileFullName = $newFilePath;

      //store file in file_log directory with orignal name character escaped
      if(!file_exists( $newFilePath ) ) {
	      if( is_uploaded_file( $this->fileArr["tmp_name"]) ) {
	         if( move_uploaded_file( $this->fileArr["tmp_name"], $newFilePath ) ){ //moving file to upload folder $newFilePath
	         	$this->fileArr["tmp_name"] = $newFilePath; //assign new file path
	         } else {
	            throw new Exception("Unable to save file.");
	         }
	      } else {
	      		throw new Exception("File did not upload.");
	      } 
      } else { //debug
      		throw new Exception("File already existed. " . $newFilePath);
      }
    }


}

?>