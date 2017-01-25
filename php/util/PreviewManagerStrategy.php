<?php
	// require_once(__DIR__.'/../config/Config.php');
	require_once(__DIR__."/../util/FileUtilities.php");
	require_once(__DIR__."/../util/POController.php");
	
	/*
	* This class holds the different algorithm for generating preview based on original file extension
	* NOTE: This does not validate extension, relies on the calling function to validate before calling
	* also, it relies on the fact that a pdf file with same prefix of the parameter is created.
	*/
	class PreviewManagerStrategy {
		private $strategy = NULL;

		/*
		* Constructor 
		* @param configRefObj - reflection object of the Config static class
		* @param filePrefix - prefix of file, required to make preview folder
		* @param oExtension - original extension of file
		* @param total - page amount
		*/
		public function __construct($configRefObj,$filePrefix,$oExtension,$pageAmount){
			// $configObj->getMethod('getFileStoragePath')->invoke(null);

			$extensionList = $configRefObj->getStaticPropertyValue('extensions');
			// print_r($configObj->getMethods() );

			switch ($oExtension) {
				case 'pdf':
				case 'pptx':
				case 'ppt':
				case 'odt':
				case 'docx':
				case 'doc':
					// $this->strategy = new ImagePreviewGeneratorForPDF($configRefObj,$filePrefix,$oExtension,$pageAmount);
					// break;
				case 'jpeg':
				case 'png':
				case 'jpg':
					// $this->strategy = new ImageExtHandler($maxSpoolTimer,$spoolPath,$storagePath,$fileName,$oExtension);
					// $this->strategy = new ImagePreviewGeneratorForPDFExt($configRefObj,$filePrefix,$oExtension,$pageAmount);
					$this->strategy = new PDFPreviewGenerator($configRefObj,$filePrefix,$oExtension,$pageAmount);
					break;
				default: 
					//concat extenion list
					$extList = '';
					foreach($extensionList as $ext => $mimeType) {
						$extList .= "$ext, ";
					}
					throw new Exception("Unsupported preview extension. Please upload file with " . 
																rtrim($extList, ', zip , ') . 
																" extenions.");
					break;
			}
		}

		/*
		* Get the preview link(s) of the preview document(s)
		* @param string:range - formatted printing range '1-2,5,9', NULL for default range
		* @param string:layout - landscape|portrait
		* @param bool:relativePathBool - true return relative path/false return full path
		* @param bool:reverseOrder - fetch in reverse order, use NULL for default 
		* @return array of all the path of the desired images 
		*/
		public function getPreviewLinks( $relativePathBool = true, $range = null, $layout='portrait', $reverseOrder = null ) {
			return $this->strategy->getPreviewLinks( $relativePathBool, $range, $layout, $reverseOrder );
		}

		public function getOriginalFilePath(){
			return $this->strategy->getOriginalFilePath();
		}

	}

	/*
	* Abstract class for image producing preview generator
	*/
	abstract class PreviewGenerator {
		protected $configRefObj; 	//config reflection object
		protected $storagePath;   //file storage path
		protected $filePrefix;	//file prefix, stored in SESSION
		protected $pdfFilePath;   //full path to source pdf file
		protected $outputPreviewExt;    //output preview extenion
		protected $previewImageWidth; //output preview width in pixels
		protected $previewImageResolution; //output preview resolution in pixels/inch
		protected $printAppRoot;  //printer app root directory
		protected $maxPDFGenerationWaitTime; //maximum wait time for pdf preview generation in second
		protected $maxPreviewConversionAtATime; //maximum number of preview conversion each time
		protected $previewFolderPath; //preview folder path, concat of storagePath and filePrefix
		protected $previewFileName;
		protected $pageAmount; 	//total document page amount
		protected $cpdfPath; 	//cpdf path
		protected $previewConvertedCount;	

		/*
		* Constructor 
		* @param configRefObj - reflection object of the Config static class
		* @param filePrefix - prefix of file, required to make preview folder
		* @param oExtension - original extension of file
		* @param total - page amount
		*/
		public function __construct($configRefObj,$filePrefix,$oExtension,$pageAmount){
			//this line relies on the assumption that the file is pdf
			$this->pdfFilePath = $configRefObj->getMethod('getFileStoragePath')->invoke(null)."$filePrefix.pdf";  

			$extensions = $configRefObj->getStaticPropertyValue('extensions');

			if( 'pdf' !== $ext = FileUtilities::determineFileType( $extensions,$this->pdfFilePath )) {
				throw new Exception("File is not a pdf");
			} else {
				$this->configRefObj = $configRefObj;
				$this->cpdfPath = $configRefObj->getMethod('getCpdfPath')->invoke(null);
				$this->filePrefix = $filePrefix;
				$this->storagePath = $configRefObj->getMethod('getFileStoragePath')->invoke(null);
				$this->previewFolderPath = $this->storagePath . $this->filePrefix;
				$this->printAppRoot = $configRefObj->getMethod('getPrinterAppRoot')->invoke(null);
				$this->maxPreviewConversionAtATime = $configRefObj->getStaticPropertyValue('maxPreviewConversionAtATime'); 
				$this->previewImageWidth = $configRefObj->getStaticPropertyValue('previewImageWidth');
				$this->previewImageResolution = $configRefObj->getStaticPropertyValue('previewImageResolution');
				$this->maxPDFGenerationWaitTime = $configRefObj->getStaticPropertyValue('maxSpoolTimer');
				$this->pageAmount = $pageAmount;
				$this->previewFileName = "$filePrefix.".$configRefObj->getStaticPropertyValue('previewFileSubfix').strval(microtime(true));
				$this->previewConvertedCount = 0;

				//getting preview extension
				if(preg_match( '/png|jpg|jpeg|pdf/', $configRefObj->getStaticPropertyValue('previewExtension') ) === 1) {
					$this->previewExt = $configRefObj->getStaticPropertyValue('previewExtension');
				} else {
					throw new Exception("Invalid preview extension.");
				}
			}
		}

		/*
		* get the full path of the original file
		*/
		public function getOriginalFilePath() {
			return $this->pdfFilePath;
		}

		/*
		* Create a preview folder
		* @return bool:true on success or false on fail
		*/
		protected function createPreviewFolder() {

			if( !mkdir($this->previewFolderPath,0774,false) ){
				return false;
			} else {
				return true;
			}

		}

		/*
		* Rearrage the array into 2 directional array, i.e. first, then last, then second, then second last
		* e.g. Array(1,2,3,4,5,6,7,8,9) =>  Array(1,9,2,8,3,7,4,6,5) 
		* @param pageList - original array
		* @return a new array with 2 direction order
		*/
		protected function array_two_direction($pagesList) {
			$twoDirectionList = array();
			$iterator1 = 0;						//starts at 0
			$iterator2 = count($pagesList)-1;   //start at last index

			for( $i = 0; $i < count($pagesList)/2; $i++ ) {		//iterate thru half the original array

				array_push( $twoDirectionList, $pagesList[$iterator1++] );	//push forward direction element in

				/*
				* Check if new array length is < original length, this
				* effectively will prevent double push of the same element
				*/
				if(count($twoDirectionList) < count($pagesList)) {			
					array_push($twoDirectionList,  $pagesList[$iterator2--] );
				}
			}

			return $twoDirectionList;
		}

		/*
		* Get default page range based on total document page amount, 1-totalPages
		* @return string: page range
		*/
		protected function getDefaultPageRange() {
			return (intval($this->pageAmount) > 1) ? "1-$this->pageAmount" : '1';
		}

		/*
		* Get the list of pages to iterate through,
		* @param range - formatted printing range '1-2,5,9'
		* @param reverseOrder - boolean, fetch in reverse order, use NULL for default. Default 2 direction
		* 			mode will fetch images in 2 directions at a time. i.e. fetch first, then last, 
		* 			then second, then second last, then third, then third last, etc 
		* @return array of indivial page number within range or false if there is an invalid page range
		* Note: This function does not throw exception if there is an invalid range
		*/
		protected function getPagesList($range, $reverseOrder){
			try {
				POController::validatePageRange($range,$this->pageAmount); //validate range
			} catch(Exception $e) {
				//echo $e->getMessage();
				return false;
			}

			//Compute page list
			//Note: at this stage the range should be in correct format
			$pagesList = array();		//initialize return array
			$numSections = preg_split('/[\,]/', $range); 

			//loop through each number section
			foreach($numSections as $numSection) {
				if( false === strpos($numSection,'-') ) { // single number case
					array_push($pagesList,intval($numSection)); //stashing in
				} else {									// pair of numbers case
					$numPair = preg_split('/[\-]/', $numSection); // split the pair
					$lowerBound = intval($numPair[0]);				//init the lower bound
					$upperBound = intval($numPair[1]);				//init upper bound
					
					for($i = $lowerBound; $i <= $upperBound; $i++) { //loop through bound
						array_push($pagesList,$i);					//stashing in
					}
				}
			}	

			//Manipulate page list to desired order
			if($reverseOrder === true) { 			//check if reversed order is desired
				return array_reverse($pagesList);
			} else if ($reverseOrder === null) {	//check if default 2 direction order is desired
				return $this->array_two_direction($pagesList);
			} else {
				return $pagesList;
			}
		}

		/*
		* Get the preview link(s) of the preview document(s)
		* @param string:range - formatted printing range '1-2,5,9'
		* @param string:layout - landscape|portrait
		* @param bool:relativePathBool - true return relative path/false return full path
		* @param bool:reverseOrder - fetch in reverse order, use NULL for default 
		* @return array of all the path of the desired images 
		*/
		abstract public function getPreviewLinks($relativePathBool, $range, $layout, $reverseOrder);

	}

	class PDFPreviewGenerator extends PreviewGenerator {
		
		/*
		* Create a pdf preview file based on 
		* @param bool:relativePathBool - true return relative path/false return full path
		* @param string:range - formatted printing range '1-2,5,9'
		* @param string:layout - portrait or landscape
		* @param bool:reverseOrder - NOT actually used, it is there to comply with php OOD
		* @return path of the new pdf file
		*/
		public function getPreviewLinks( $relativePathBool, $range, $layout, $reverseOrder){
			//check if page range is specified
			if($range == NULL) {
				$range = $this->getDefaultPageRange();		//get default page range
			} else { 													//unsanitized range input
				POController::validatePageRange($range,$this->pageAmount); //validate range
			}

			//check if page layout is valid
			if($layout !== 'portrait') { 					
				POController::validatePageLayout($layout); //valid layout
			}

			//check if preview folder exist
			if(!file_exists($this->previewFolderPath)) { //if not make a folder
				//open preview folder
				$this->createPreviewFolder();
			} 

			//check if file path is readable
			if (! is_readable($this->pdfFilePath)) {
	    		throw new Exception("File not readable");
			}

			//make preview with range option
			$previewFilePath = $this->makePreviewFromRange($range,$this->pdfFilePath);

			//make preview with layout option 
			// $secondPreviewFilePath = $this->makePreviewFromLayout($layout, $previewFilePath);
 			$secondPreviewFilePath = $this->makePreviewFromLayout($layout,$previewFilePath);
			//checking if relative path is desired
			if($relativePathBool === true) {
				list($k,$v) = explode("$this->printAppRoot/", $secondPreviewFilePath); //fetch the relative string by spliting the differences
				return $v; 	
			} else {
				return $secondPreviewFilePath;
			}

		}


		/*
		* Make preview based on range
		* @param range - formatted printing range '1-2,5,9'
		* @param srcFilePath - source path of file
		* @return link to the new preview file
		* @exception Failed to make preview from range
		* NOTE: This method does not validate range format, it must be done by the calling method.
		* Also, this function intentionally override the 
		*/
		private function makePreviewFromRange($range,$srcFilePath = null) {
			$outputName = "$this->previewFolderPath/$this->previewFileName.pdf";
			$command = "$this->cpdfPath $srcFilePath $range -o $outputName 2>&1";

			exec($command, $response, $result);

			if($result != 0) {	//upload failed
				throw new Exception("Failed to make preview from range $result " . implode(' ',$response));
			}

			$this->waitForFileToFinish($outputName, $this->maxPDFGenerationWaitTime);

			return $outputName;
		}

		/*
		* Make preview based on layout
		* @param layout - string landscape or portrait
		* @param srcFilePath - source path of file
		* @return link to the new preview file
		* @exception Failed to make preview from layout
		* NOTE: This method does not validate layout format, it must be done by the calling method
		*/
		private function makePreviewFromLayout($layout,$srcFilePath = null) {
			$outputName = "$this->previewFolderPath/$this->previewFileName.pdf";
			$command = "$this->cpdfPath -scale-to-fit a4$layout $srcFilePath -o $outputName 2>&1"; //command for rotating page to scale


			exec($command, $response, $result);

			if($result != 0) {	//upload failed
				throw new Exception("Failed to make preview from layout $result " . implode(' ',$response));
			} 
			// else {
			// 	throw new Exception("Failed to make preview from layout $command $result " . implode(' ',$response));
			// }

			//wait for file to finish loading before continuing
			$this->waitForFileToFinish($outputName, $this->maxPDFGenerationWaitTime);

			return $outputName;
		}

		/*
		* Wait for file to finish loading
		* @param srcFilePath - the file path to check for 
		* @param maxWaitTime - the max wait time in seconds
		* @exception - File preview generator is taking too long. Please contact an officer for help
		*/
		private function waitForFileToFinish($srcFilePath,$maxWaitTime) {
			sleep(1);
			$uTime = time(); //create a timer
			$processing = true; 	  //flag to check if still processing
			while ($processing) {
				if( file_exists($srcFilePath) ) { //if not make a folder
					$processing = false;
				} else if(time() - $uTime > $maxWaitTimer) { //check if file has been in spool for too long
					throw new Exception("File preview generator is taking too long. Please contact an officer for help");
				}
			}
		}
	}

	/*
	* This class create preview images for NON pdf files
	*/
	class ImagePreviewGeneratorForPDFExt extends PreviewGenerator {

		/*
		* Convert pdf file to images and return list of all avaiable preview links.
		* @param string:range - formatted printing range '1-2,5,9'
		* @param bool:relativePathBool - true return relative path/false return full path
		* @param bool:reverseOrder - fetch in reverse order, use NULL for default. Default 2 direction
		* 			mode will fetch images in 2 directions at a time. i.e. fetch first, then last, 
		* 			then second, then second last, then third, then third last, etc 
		* @return array of all the path of the desired images 
		* NOTE: 
		* 		array(
					1 => "temporary_file_storage/doc1/1.png",
					2 => "temporary_file_storage/doc1/2.png",
					4 => "temporary_file_storage/doc1/4.png",
					5 => "temporary_file_storage/doc1/5.png",
					6 => "temporary_file_storage/doc1/6.png",
					7 => "temporary_file_storage/doc1/7.png",
					8 => "temporary_file_storage/doc1/8.png",
					9 => "temporary_file_storage/doc1/9.png"
				)
		*/
		public function getPreviewLinks( $relativePathBool = true, $range = null, $layout='portrait', $reverseOrder = null ){
			$imageLinks = array();
		
			//check if page range is specified
			if($range == NULL) {
				$range = $this->getDefaultPageRange();		//get default page range
			}

			//check if preview folder exist
			if(!file_exists($this->previewFolderPath)) { //if not make a folder
				//open preview folder
				$this->createPreviewFolder();
			} 

			//check if file path is readable
			if (! is_readable($this->pdfFilePath)) {
	    		throw new Exception("File not readable");
			}

			//get page number list
			if( false === $pagesList = $this->getPagesList($range,$reverseOrder) ) {
				return false;
			} 

			foreach ($pagesList as $pageNumber) { //loop through pages list
				//make preview image
				if( false !== $previewLink = $this->makeImages($pageNumber,$relativePathBool) ) {
					$imageLinks[$pageNumber] = $previewLink; 
					// array_push($imageLinks, $previewLink);
				}
			}
			return $imageLinks;
			// print_r($pagesList);
		}

		/*
		* Convert pdf page to image based on page number input
		* @param string:pageNum - single page number to print'1-2,5,9'
		* @param bool:relativePathBool - true return relative path/false return full path
		* @return full/relative path link to the preview image or null if conversion limit is reached
		*/
		private function makeImages($pageNum, $relativePathBool = true){

			//adjusted from index value to counting value
			$pageIndex = intval($pageNum) - 1; 
			
			//make the image link
			$imageLink = "$this->previewFolderPath/$pageNum.$this->previewExt";
			
			//check if image exist if not make the imgage
			if( !file_exists($imageLink) ) {
				// echo "$pageNum doesn not exist \n";
				if( $this->previewConvertedCount < $this->maxPreviewConversionAtATime ) { //check if conversion limit is reached
					
					//create the image
					$imagick = new Imagick(); 
					//$imagick->setSize(800,600);
					$imagick->setResolution( $this->previewImageResolution, $this->previewImageResolution );
					$imagick->readImage("$this->pdfFilePath[$pageIndex]");
					// $imagick->resizeImage ( 350, 0,  Imagick::FILTER_CATROM, 1, TRUE); //resize while keeping aspect ratio
					$imagick->scaleImage( $this->previewImageWidth,0 ); 		//scaling image to width while preserving resolution
					// $imagick->thumbnailImage(400, 300, true, true);
					$imagick = $imagick->flattenImages();
					if( true !== $imagick->writeImage($imageLink) ) {
						throw new Exception("Failed to write preview images");
					}
					$this->previewConvertedCount++; 		//increment converted count

					return $this->makeImages($pageNum,$relativePathBool); //recursive call to return image url
				} else {
					return null;
				}
			} else {
				//check if relative path mode is desired
				if($relativePathBool === true) {
					list($k,$v) = explode("$this->printAppRoot/",$imageLink);
					return $v; 			//return relative path
				} else {
					return $imageLink; //return full path
				}
			}	
		}
	}

	// /*
	// * This class create preview images for pdf files
	// */
	// class ImagePreviewGeneratorForPDF extends PreviewGenerator {

	// }
?>