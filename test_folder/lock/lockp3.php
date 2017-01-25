<?php
/*
	* @param path to the file
	* @return string number of pages 
	* @exception thrown when no pages amount if found
	*/
	function getPageAmount($path) {
		//Retrieving number of pages in this file using "xpdf"
	    exec ('pdfinfo ' . $path . ' | awk \'/Pages/ {print $2}\'', $xpdfOutput);
	    print_r($xpdfOutput);
		/*if( count($xpdfOutput) > 0 ) {
			return $xpdfOutput[0];
			//throw new Exception('Cannot determine page amount!');
		} else { //no page amount found
			throw new Exception('Cannot determine page amount! ' );
		}*/
	}

	getPageAmount('/home/duy/Documents/FACIL/Math31W/Facil/calculus31.pdf');
?>