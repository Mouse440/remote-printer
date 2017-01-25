<?php
        $arr = array('demo.pdf',
			        'doc11.xls',
			        'doc12.xlsx',
			        'doc13.odt',
			        'doc2.docx',
			        'doc3.pptx',
			        'doc4.jpg',
			        'doc5.doc',
			        'doc7.png',
			        'doc8.jpeg',
			        'doc9.ppt',
			        'doc14.docx',
			        'doc15.zip');


	$finfo = new finfo(FILEINFO_MIME_TYPE);
	for($i = 0; $i < count($arr); $i++) {
	    if (false === $ext = array_search(
	        $finfo->file('testdocs/'.$arr[$i]),
	        array(
	        	'pdf' => 'application/pdf',
	            'jpg' => 'image/jpeg',
	            'png' => 'image/png',
	            'doc' => 'application/msword',
	            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	            'ppt' => 'application/vnd.ms-powerpoint',
	            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	            'odt' => 'application/vnd.oasis.opendocument.text',
	            // 'xls' => 'application/vnd.ms-excel',
	            // 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
	        ),
	        true
	    )) {
	        // throw new RuntimeException('Invalid file format.');
	        echo 'Invalid file format. ' . $arr[$i] . ' ' . $finfo->file('testdocs/'.$arr[$i]) . "\n"; 
	    } else {
	        echo 'File ok! ' . $arr[$i] . ' ' . $finfo->file('testdocs/'.$arr[$i]) . " $ext \n"; 
	    }
	}
?>