<?php
	require_once(__DIR__."/../php/config/Config.php");
	require_once(__DIR__."/../php/util/FileUtilities.php");

	// echo FileUtilities::convertStorageFileToPDF(
	// 									new ReflectionClass('Config'),
	// 									'doc5',
	// 									'doc');

	/*
		* Make preview based on layout
		* @param layout - string landscape or portrait
		* @param srcFilePath - source path of file
		* @return link to the new preview file
		* @exception Failed to make preview from layout
		* NOTE: This method does not validate layout format, it must be done by the calling method
		*/
		function makePreviewFromLayout($layout,$srcFilePath = null) {
			// $outputName = "$this->previewFolderPath/$this->previewFileName.pdf";
			$outputName = "/var/www/html/sceprinterv3/temporary_file_storage/docProp.pdf";
			$command = "/var/www/html/sceprinterv3/lib/cpdf-binaries-master/Linux-Intel-64bit/cpdf -scale-to-fit a4$layout $srcFilePath -o $outputName"; //command for rotating page to scale

			exec($command, $response, $result);

			// if($result != 0) {	//upload failed
			// 	throw new Exception("Failed to make preview from layout $result " . implode(' ',$response));
			// }

			// //wait for file to finish loading before continuing
			// $this->waitForFileToFinish($outputName, $this->maxPDFGenerationWaitTime);

			// return $outputName;
		}

		makePreviewFromLayout('landscape','/var/www/html/sceprinterv3/temporary_file_storage/doc5.pdf');

?>