<?php

/*
* DEPRECATED
*/
	// require_once(__DIR__."/../util/show-error.php");
	// require_once(__DIR__."/../config/Config.php");
	// require_once(__DIR__."/../util/PreviewManagerStrategy.php");

	// /*
	// * This script fetch more preview links based on previous transaction information. 
	// * It will fetch all the preview image link avaible while make new ones that does not exist yet. 
	// */

	// $range = ( true == isset($_POST['range']) ) ? $_POST['range'] : null; //get t

	// // //determine fetch method
	// // $fetchMethod = ($_POST['reverseFetchingNeeded'] === 'true') ? 'reverse' : 'forward';
	// // die($fetchMethod);
	// session_start();
	// //Check if fetching already
	// if( false === isset( $_SESSION['PRINT_TRANSACTION']['fetching_preview'] ) ){
	// 	$_SESSION['PRINT_TRANSACTION']['fetching_preview'] = true;   //assign mutex
	// } else {
	// 	die("Already fetching $fetchMethod");
	// }

	// session_write_close();
	// // $amount = $_SESSION['PRINT_TRANSACTION']['amount'];
	// // $filePrefix = $_SESSION['PRINT_TRANSACTION']['filePrefix']; //file prefix - without extension
	// // $fileExtension = $_SESSION['PRINT_TRANSACTION']['fileOriginalExt']; //original file extension

	// $amount = 9;
	// $filePrefix = 'doc5'; //file prefix - without extension
	// $fileExtension = 'pdf'; //original file extension

	// //Initialize a preview generator
	// $previewGen = new PreviewManagerStrategy(
	// 						new ReflectionClass('Config'),
	// 						$filePrefix,
	// 						$fileExtension,
	// 						$amount);

	// //fetch preview links
	// $result = array(
	// 	'previewLinks' => $previewGen->getPreviewLinks(true,$range), //preview links based on method
	// 	// 'fetchMethod' => $fetchMethod
	// 	);

	// session_start();
	// //clearing mutex
	// unset($_SESSION['PRINT_TRANSACTION']['fetching_preview']);
	// session_write_close();

	// echo json_encode($result);
?>