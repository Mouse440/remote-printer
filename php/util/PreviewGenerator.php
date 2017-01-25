<?php
require_once(__DIR__."/../config/Config.php");
require_once(__DIR__."/FileUtilities.php");
require_once(__DIR__."/POController.php");

/*
* DEPRECATED
* This class is responsible for generating preview images
* Note: Once a preview file is made, it will not be remade again. This fact is used to 
* leverage iterative file fetching mechanism. That is, once a page already has a preview,
* it will be ignored and the next page will be looked at. There are at most $$maxPreviewConversionAtATime 
* per request
*/
class PreviewGenerator {
	private $storagePath;   //file storage path
	private $filePrefix;	//file prefix, stored in SESSION
	private $pdfFilePath;   //full path to pdf file
	private $previewExt;    //output preview extenion
	private $previewImageWidth; //output preview width in pixels
	private $previewImageResolution; //output preview resolution in pixels/inch
	private $printAppRoot;  //printer app root directory
	private $maxPreviewConversionAtATime; //maximum number of preview conversion each time
	private $previewFolderPath; //preview folder path, concat of storagePath and filePrefix
	private $pageAmount; 	//total document page amount

	private $previewConvertedCount;	

	/*
	* Constructor
	* @param filePrefix - prefix of file, required to make preview folder
	* @param total - page amount
	*/
	public function __construct($filePrefix,$pageAmount){
		//this line relies on the assumption that the file is pdf
		$this->pdfFilePath = Config::getFileStoragePath()."$filePrefix.pdf";  

		if( 'pdf' !== $ext = FileUtilities::determineFileType($this->pdfFilePath)) {
			throw new Exception("File is not a pdf");
		} else {
			$this->storagePath = Config::getFileStoragePath();
			$this->filePrefix = $filePrefix;
			$this->previewFolderPath = $this->storagePath.$this->filePrefix;
			$this->printAppRoot = Config::getPrinterAppRoot();
			$this->maxPreviewConversionAtATime = Config::$maxPreviewConversionAtATime; 
			$this->previewImageWidth = Config::$previewImageWidth;
			$this->previewImageResolution = Config::$previewImageResolution;
			$this->pageAmount = $pageAmount;
			$this->previewConvertedCount = 0;
			if(preg_match('/png|jpg|jpeg/', Config::$previewExtension) === 1) {
				$this->previewExt = Config::$previewExtension;
			} else {
				throw new Exception("Invalid preview extension.");
			}
		}

	}

	/*
	* Convert pdf file to images and return preview links.
	* @param string:range - formatted printing range '1-2,5,9'
	* @param bool:relativePathBool - true return relative path/false return full path
	* @param bool:reverseOrder - fetch in reverse order, use NULL for default 
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
	public function getPreviewImageLinks( $relativePathBool = true, $range, $reverseOrder = false ) {
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
    		throw new Exception('File not readable');
		}

		//get page number list
		if( false === $pagesList = $this->getPagesList($range) ) {
			return false;
		} else {

			if($reverseOrder == true) { //check if reversed order is desired
				$pagesList = array_reverse($pagesList);
			}

			foreach ($pagesList as $pageNumber) { //loop through pages list
				//make preview image
				if( false !== $previewLink = $this->makeImages($pageNumber,$relativePathBool) ) {
					$imageLinks[$pageNumber] = $previewLink; 
					// array_push($imageLinks, $previewLink);
				}
			}
		}

		return $imageLinks;
		// print_r($pagesList);
	}

	/*
	* Get default page range based on total document page amount, 1-totalPages
	* @return string: page range
	*/
	private function getDefaultPageRange() {
		return (intval($this->pageAmount) > 1) ? "1-$this->pageAmount" : '1';
	}

	/*
	* Get the list of pages to iterate through
	* @param range - formatted printing range '1-2,5,9'
	* @param pageAmount - original page amount
	* @return array of indivial page number within range or false if there is an invalid page range
	* Note: This function does not throw exception if there is an invalid range
	*/
	private function getPagesList($range){
		try {
			POController::validatePageRange($range,$this->pageAmount); //validate range
		} catch(Exception $e) {
			echo $e->getMessage();
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

		return $pagesList;
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
		
		//check if image exist
		if( !file_exists($imageLink) ) {//if not make the imgage
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
			//check if relativepath mode is desired
			if($relativePathBool === true) {
				list($k,$v) = explode("$this->printAppRoot/",$imageLink);
				return $v; 			//return relative path
			} else {
				return $imageLink; //return full path
			}
		}	
	}

	/*
	* Create a preview folder
	* @return string - path to preview folder or false on fail
	*/
	public function createPreviewFolder() {

		if( !mkdir($this->previewFolderPath,0774,false) ){
			return false;
		} else {
			return $folderPath;
		}

	}

}
?>