<?php 

#extension type check extension types
	$rootPath = '/Users/duynguyen/Downloads/';
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$fileNames = exec('cd ~/Downloads/ && ls -a *.docx 2>&1',$r1,$r2);
	for($i=0;$i<count($r1); $i++) {

		// $mimeType = $finfo->file($rootPath.$r1[$i]); //get mime type
		// echo $r1[$i]. " => $mimeType\n";
		exec("file -i " . $rootPath.$r1[$i] . " 2>&1",$res);
		print_r($res);
	}