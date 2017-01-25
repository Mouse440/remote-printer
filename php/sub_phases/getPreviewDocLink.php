<?php
/*
* Controler
* Get the preview link based on options 
*/
	require_once(__DIR__.'/../security/checkLoginV3.php');
	require_once(__DIR__."/../util/show-error.php");
	require_once(__DIR__."/../config/Config.php");
	require_once(__DIR__."/../util/PreviewManagerStrategy.php");

	session_start();
	$filePrefix = $_SESSION['PRINT_TRANSACTION']['filePrefix'];
	$fileOriginalExt = $_SESSION['PRINT_TRANSACTION']['fileOriginalExt'];
	$amount = $_SESSION['PRINT_TRANSACTION']['amount'];

	$range = $_POST['pageRange'];
	$layout = $_POST['layout'];

	$previewManagerStrategy = new PreviewManagerStrategy(
											new ReflectionClass('Config'),
											$filePrefix,
											$fileOriginalExt,
											$amount);

	$fileToPrintFullPath = $previewManagerStrategy->getPreviewLinks(true,$range,$layout); //file to print the preview link
	$_SESSION['PRINT_TRANSACTION']['fileToPrintFullPath'] = $fileToPrintFullPath; //full file name, this name will be the file print

	// print_r($_SESSION);
	echo $fileToPrintFullPath;
?>