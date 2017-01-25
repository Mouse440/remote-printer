<?php
// if (! is_readable('testdocs/b3182387eac71f277da25ab5be7ee94e6ab32c49.1434496027.38.pdf')) {
//     echo 'file not readable';
//     exit();
// }
//  $imagick = new Imagick(); 
//  $imagick->setSize(800,600);
//  $imagick->setResolution(150, 150);
//  $imagick->readImage('testdocs/b3182387eac71f277da25ab5be7ee94e6ab32c49.1434496027.38.pdf'); 
//  $imagick = $imagick->flattenImages();
//  $imagick->writeImage('asdasd.png'); 
//phpinfo();

	$path = '/var/www/html/sceprinterv3/test_folder/testdocs';


 	/*
	* Convert pdf page to image based on page number input
	* @param string:pageNum - single page number to print'1-2,5,9'
	* @param bool:relativePathBool - true return relative path/false return full path
	* @return full/relative path link to the preview image or null if conversion limit is reached
	*/
	function makeImages($pdfFilePath,$path){

		//adjusted from index value to counting value
		
		//make the image link
		$imageLink = "$path/out.png";
		
		// echo "$pageNum doesn not exist \n";
			
		//create the image
		$imagick = new Imagick(); 
		//$imagick->setSize(800,600);
		$imagick->setResolution( 140, 140 );
		$imagick->readImage("$pdfFilePath");
		// $imagick->resizeImage ( 350, 0,  Imagick::FILTER_CATROM, 1, TRUE); //resize while keeping aspect ratio
		// $imagick->scaleImage( 400,0 ); 		//scaling image to width while preserving resolution
		// $imagick->thumbnailImage(400, 300, true, true);
		$imagick = $imagick->flattenImages();
		if( true !== $imagick->writeImage($imageLink) ) {
			throw new Exception("Failed to write preview images");
		}
	}

	makeImages("$path/doc2.pdf",$path);
?>