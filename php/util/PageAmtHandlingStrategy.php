<?php
	/*
	* This class holds the different algorithm for getting page amount of file base on extension
	* NOTE: This does not validate extension, relies on the calling function to validate before calling
	*/
	class PageAmtHandlingStrategy {
		private $strategy = NULL;

		/*
		* Constructor 
		* @param storagePath - path to the storage directory
		* @param fileName - name of file to reference
		* @param oExtension - original extension of such file
		*/
		public function __construct($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension){
			switch ($oExtension) {
				case 'jpeg':
				case 'png':
				case 'jpg':
					$this->strategy = new ImageExtHandler($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension);
					break;
				case 'pptx':
				case 'ppt':
				case 'odt':
				case 'docx':
				case 'doc':
					$this->strategy = new MSExtHandler($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension);
					break;
				// case 'doc':
				// 	$this->strategy = new DocExtHandler($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension);
				// 	break;
				case 'pdf':
					$this->strategy = new PDFExtHandler($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension);
					break;
				default: 
					//concat extenion list
					$extList = '';
					foreach(Config::$extensions as $ext => $mimeType) {
						$extList .= "$ext, ";
					}
					throw new Exception("Unsupported extension. Please upload file with " . 
																rtrim($extList, ', zip , ') . 
																" extenions.");
					break;
			}
		}

		/*
		* Get page amount of file
		* @return string : page amount
		*/
		public function getPageAmount(){
			return (string)$this->strategy->findAmount();
		}

		/*
		* Get new file name with new extension
		* @return string : new file name
		*/
		public function getNewFileName() {
			return (string)$this->strategy->createNewName();
		}
	}
	
	/*
	* Abstract class
	*/
	abstract class FileHandler {
		protected $storagePath = NULL;
		protected $spoolPath = NULL;  //
		protected $fileName = NULL;
		protected $oExtension = NULL; //original extension
		protected $maxSpoolTimer = NULL; //max spool timer

		/*
		* Constructor 
		* @param maxSpoolTimer - max spool timer 
		* @param spoolPath - path to the spool directory
		* @param storagePath - path to the storage directory
		* @param fileName - name of file to reference
		* @param oExtension - original extension of such file
		*/
		public function __construct($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension) {
			$this->maxSpoolTimer = $maxSpoolTimer;
			$this->spoolPath = $spoolPath;
			$this->storagePath = $storagePath;
			$this->fileName = $fileName;
			$this->oExtension = $oExtension;
	    }	

		/*
		* Convert file to pdf.
		* Note: this method does not guarantee the integrity of the document, Meaning the conversion 
		* could modify the original content of the file
		* @return string:spitLocation - the new location of file + file_name.pdf
		* @exception - An error has occur while converting document. Try uploading a pdf copy instead.
		*/
		protected function convertFileToPDF() {
			$newExtension = 'pdf';
			$softwareName = 'libreoffice';
			$storagePath = $this->storagePath; 				//storage path
			$oFileName = $this->fileName.'.'.$this->oExtension;		//original file name
			$oFileFullPathName = $this->storagePath.$oFileName;		//original file full path
			$newFileName = $this->fileName.'.'.$newExtension;		//new file name
			$newPathFileFullName = $this->storagePath.$newFileName; // new file path full name
			$spoolPath = $this->spoolPath;							//spool path
			$maxSpoolTimer = $this->maxSpoolTimer;					//max spool timer

			session_start();
			session_write_close();
			$soffice = $_SESSION['_USER_']['SOFFICE'];
			if( $soffice === true ) { // SOFFICE variable is present, Mac osx only
				$softwareName = '/Applications/LibreOffice.app/Contents/MacOS/' . 'soffice';

				// building command
				$command = sprintf('export HOME=/tmp && %s --headless --convert-to pdf --outdir %s %s 2>&1',
																					$softwareName,
																					rtrim($storagePath,'/'), //trim traling '/' if any
																					$oFileFullPathName);
				exec($command,$spit,$status);

				if ( in_array('Error',$spit) == true || $status != 0 ) { //An error has occur
				    throw new Exception("An error has occur while converting document. Try uploading a pdf copy instead. $soffice $status $softwareName "  . implode( ' ', $spit ) );
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

		abstract public function findAmount();
		abstract public function createNewName();
	}

	/*
	* Handler for files with extenion .pdf 
	*/
	class PDFExtHandler extends FileHandler {
		private $pdfExt = 'pdf';


		/*
		* Find amount of the pdf document
		* @param optional string: full path to document
		* @return string: document amount
		* @exception - Original extension is now pdf.
		* @exception - Cannot determine page amount!
		*/
		public function findAmount($path = NULL) {

			// $this->fileName = "asdasds"; //testing

			if($this->oExtension != 'pdf' && NULL == $path) {
				throw new Exception('Original extension is not pdf.');
			}

			$fullPath = ($path === NULL) ? $this->storagePath.$this->fileName.'.'.$this->oExtension : $path;

			//Retrieving number of pages in this file using "xpdf"
		    exec ('pdfinfo ' . $fullPath . ' | awk \'/Pages/ {print $2}\'', $xpdfOutput, $r);
			if( count($xpdfOutput) > 0 ) {
				$lastIndex = count($xpdfOutput) - 1;				//sometimes $xpdfOutput will yield [0]->'Pages' [1]->Page #
				return $xpdfOutput[ $lastIndex ]; 					
				//throw new Exception('Cannot determine page amount!');
			} else { //no page amount found
				throw new Exception('Cannot determine page amount! '.$fullPath );
			}
		}

		/*
		* Create new file name
		* @return string: new file name with pdf extension
		*/
		public function createNewName() {
			return $this->fileName.'.'.$this->pdfExt;
		}
	}

	/*
	* Handler for images with extension .jpg .jpeg .png
	*/
	class ImageExtHandler extends PDFExtHandler{
		// private $imgExt;
		// public function __construct($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension) {
		// 	parent::__construct($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension);
		// 	$this->imgExt = $this->oExtension;
	 //    }	

	    /*
		* Find amount of the pdf document
		*/
		public function findAmount() {
			$pdfCopyPath = $this->convertFileToPDF();
			return parent::findAmount($pdfCopyPath);
		}

		/*
		* Create new file name
		* @return string: new file name with pdf extension
		*/
		public function createNewName() {
			// return $this->fileName.'.'.$this->imgExt;
			return parent::createNewName();
		}
	}

	/*
	* Handler for Microsoft extensions .pptx .ppt .odt .docx
	* Notice that this Handler does not include .doc 
	*/
	class MSExtHandler extends PDFExtHandler {
		private $pdfExt = 'pdf';

		/*
		* Find amount of the pdf document
		*/
		public function findAmount() {
			$pdfCopyPath = $this->convertFileToPDF();
			return parent::findAmount($pdfCopyPath);
		}

		/*
		* Create new file name
		* @return string: new file name with pdf extension
		*/
		public function createNewName() {
			return parent::createNewName();
		}
	}

	/*
	* This class handles the .doc extension files
	*/
	class DocExtHandler extends PDFExtHandler {
		/*
		* Find amount of the pdf document
		* @return string: page amount of doc
		*/
		public function findAmount() {
			$fullPath = "$this->storagePath/$this->fileName.$this->oExtension";
			$command = "file $fullPath";

			exec($command,$arr,$r);	
			if( $r == 0) {
				$pattern = '/Number of Pages: \d+/';		
				preg_match($pattern, $arr[0], $matches);	//dig through the result for a match to pattern
				$numOfPagesArr = explode(':',$matches[0]);
				$pageAmount = trim( $numOfPagesArr[1] );
				return $pageAmount;
			} else {
				throw new Exception( 'Cannot determine .doc page amount. Try uploading a pdf copy. ' . implode(' ',$arr) . " $r " . $fileTempName );
			}
			// $pdfCopyPath = $this->convertFileToPDF();
			// return parent::findAmount($pdfCopyPath);
		}

		/*
		* Create new file name
		* @return string: new file name with pdf extension
		*/
		public function createNewName() {

			if( false == $this->convertFileToPDF() ) {			//Convert content to pdf
				throw new Exception('Error converting to pdf.');
			}
			return parent::createNewName(); 
		}
	}
	
	// class ImageExtHandler extends FileHandler{
	// 	// private $imgExt;
	// 	// public function __construct($storagePath,$fileName,$oExtension) {
	// 	// 	parent::__construct($storagePath,$fileName,$oExtension);
	// 	// 	$this->imgExt = $this->oExtension;
	//  //    }	

	// 	public function findAmount() {
	// 		$pdfCopyPath = $this->convertFileToPDF();

	// 		$fullPath = $pdfCopyPath;

	// 		//Retrieving number of pages in this file using "xpdf"
	// 	    exec ('pdfinfo ' . $fullPath . ' | awk \'/Pages/ {print $2}\'', $xpdfOutput, $r);
	// 		if( count($xpdfOutput) > 0 ) {
	// 			$lastIndex = count($xpdfOutput) - 1;				//sometimes $xpdfOutput will yield [0]->'Pages' [1]->Page #
	// 			return $xpdfOutput[ $lastIndex ]; 					
	// 			//throw new Exception('Cannot determine page amount!');
	// 		} else { //no page amount found
	// 			throw new Exception('Cannot determine page amount! '.$fullPath );
	// 		}
	// 		// return parent::findAmount($pdfCopyPath);
	// 	}

	// 	public function createNewName() {
	// 		return $this->fileName.'.'.$this->oExtension;
	// 	}
	// }
	
?>