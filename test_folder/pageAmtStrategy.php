<?php
	session_start();
	$_SESSION['PRINT_APP_ROOT_DIR'] = __DIR__;
	$_SESSION['SOFFICE'] = false;
	session_write_close();
	require_once(__DIR__.'/../php/config/Config.php');
	require_once(__DIR__.'/../php/util/PageAmtHandlingStrategy.php');

	$extensions = array(
	        	'pdf' => 'application/pdf',
	            'jpg' => 'image/jpeg',
	            'png' => 'image/png',
	            'doc' => 'application/msword',
	            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	            'ppt' => 'application/vnd.ms-powerpoint',
	            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	            'odt' => 'application/vnd.oasis.opendocument.text',
	            'zip' => 'application/zip',
	            // 'xls' => 'application/vnd.ms-excel', //not supported
	            // 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' //not supported
	        );  //File extension allowed 


	$exp1 = new PageAmtHandlingStrategy(Config::$maxSpoolTimer,Config::getSpoolDirPath(),Config::getFileStoragePath(),'doc5','doc');

	// echo $exp1->getPageAmount() . "\n";
	echo $exp1->getNewFileName() . "\n";

	// foreach($extensions as $k => $v) {
	// 	echo "k is $k => ";
	// 	$exp1 = new PageAmtHandlingStrategy($k);
	// 	echo "\n";
	// }

	// echo Config::getFileStoragePath();


	// $filePath = '/var/www/html/sceprinterv3/temporary_file_storage/doc5.doc';
	// $exportPath = '/var/www/html/sceprinterv3/temporary_file_storage/doc5.pdf';

	'/home/duy/Downloads/cde-libreoffice-pdf'
?>