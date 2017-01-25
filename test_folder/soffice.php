<?php
	require(__DIR__.'/../php/phase1/dev.php');

	$time_pre = microtime(true);
	echo "start $time_pre \n";
	
	
	exec(SOFFICE.'soffice --headless --convert-to pdf --outdir '.__DIR__.'/testdocs/ testdocs/doc7.png',$spit,$status);
	//catch 'Error'
	echo $status."\n";


	$time_post = microtime(true);
	$exec_time = $time_post - $time_pre;
	echo "done, elapsed: $exec_time\n" ;
?>