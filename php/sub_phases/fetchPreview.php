<?php

	/*
	* DEPRECATED
	*/
	/*require_once(__DIR__."/../util/show-error.php");
	require_once(__DIR__."/../util/PreviewGenerator.php");

	//determine fetch method
	$fetchMethod = ($_POST['reverseFetchingNeeded'] === 'true') ? 'reverse' : 'forward';
	die($fetchMethod);
	session_start();
	//Checking for forward fetching
	if( false === isset( $_SESSION['PRINT_TRANSACTION']['forwardFetchingPreview'] ) 
																&& $fetchMethod === 'forward') {
		$_SESSION['PRINT_TRANSACTION']['forwardFetchingPreview'] = true;   //assign mutex
	} else if( false === isset($_SESSION['PRINT_TRANSACTION']['reverseFetchingPreview']) 
																 && $fetchMethod === 'reverse') { //Checking for reverse fetching
		$_SESSION['PRINT_TRANSACTION']['reverseFetchingPreview'] = true; //assign mutex
	} else {
		die("Already fetching $fetchMethod");
	}

	session_write_close();
	$amount = $_SESSION['PRINT_TRANSACTION']['amount'];
	$filePrefix = $_SESSION['PRINT_TRANSACTION']['filePrefix']; //file prefix - without extension

	//Initialize a preview generator
	$previewGen = new PreviewGenerator($filePrefix,$amount);

	//fetch preview links
	$result = array(
		'previewLinks' => $previewGen->getPreviewImageLinks(true,null,$_POST['reverseFetchingNeeded']), //preview links based on method
		// 'fetchMethod' => $fetchMethod
		);

	session_start();
	//clearing forward mutex
	if( $fetchMethod === 'forward' ) {
		unset( $_SESSION['PRINT_TRANSACTION']['forwardFetchingPreview'] );
	} 

	//clearing reverse mutex
	if( $fetchMethod === 'reverse') {
		unset($_SESSION['PRINT_TRANSACTION']['reverseFetchingPreview']);
	}
	session_write_close();

	echo json_encode($result);*/
?>