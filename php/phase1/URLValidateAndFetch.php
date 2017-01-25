<?php
	require_once(__DIR__."/../util/URLDownloaderStrategy.php");

 	$url = 'www.docx4java.org/docx4j/Docx4j_GettingStarted.docx';

 	$dler =  new URLDownloaderStrategy($url);
 	$dler->downloadFile();