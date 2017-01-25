<?php

	$filePath = 'file testdocs/doc16.doc';

	
	exec($filePath,$arr,$r);
	if( $r == 0) {
		$pattern = '/Number of Pages: \d+/';
		preg_match($pattern, $arr[0], $matches);
		$numOfPagesArr = explode(':',$matches[0]);
		$pageAmount = trim( $numOfPagesArr[1] );
		echo $pageAmount;
	}
	// echo shell_exec('file testdocs/doc5.doc | awk \'Number of Pages\''); PREG_OFFSET_CAPTURE
	
?>