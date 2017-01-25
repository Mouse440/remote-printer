<?php

/*
   * store file in file_log directory
   * @param permission - true/false asking user to allow file storage
   */
   function storeFile($permission) {
      //$fName = addslashes( $_FILES["file"]['name'] );

      if( $permission ){
         //store file in file_log directory with orignal name character escaped
          if( !file_exists('../file_log/locked.txt') ) {
            if( !move_uploaded_file( 'locked.txt'  , 'file_log/' ) ){ //moving file to upload folder
               //storeError(" Unable to save file.");
            	echo 'cant store file.';
            } 
         } //Note: Inorder for file saving to work, the file to upload to must have the right permission --6
      } else {
         //Gather information about file 
         if( !file_exists('../file_log/' . $fName.'.info.txt') ) {
            exec ('pdfinfo ' . $_FILES["file"]["tmp_name"] , $output); //fetch file information 
            foreach($output as $str) {                                  //loop through output and store into a text file in file_log
               file_put_contents("../file_log/$fName.info.txt", "$str\n", FILE_APPEND | LOCK_EX );
            }
         }
      }
   }

  // storeFile(true);

/*
	function lockFile($path,$maxWait) {
		$ttl = time() + $maxWait;
		$fp = fopen($path, 'r');
		while(time() < $ttl){							// persistently try to get a lock
			if(flock($fp, LOCK_EX)) {
				return 'true';
			}
		} 
		return 'false';
	}

	function unlockFile($path){
		$fp = fopen($path, 'r');
		flock($fp, LOCK_UN);
	}


	echo lockFile('./locked.txt', 5);
	unlockFile('./locked.txt');


	echo "Start time: " . $_SERVER['REQUEST_TIME'] . " Finish time: " . microtime(true);*/
	/*$fp = fopen('./locked.txt', 'r');

	if(flock($fp, LOCK_EX)) {
        print "Got lock!\n";
        sleep(10);
        flock($fp, LOCK_UN);
    }
    echo "Start time: " . $_SERVER['REQUEST_TIME'] . " Finish time: " . microtime(true);
*/
/*	exec('mkdir -m 777 locked',$res,$r); 
	$ttl = floatval(microtime(true)) + 5.0;
	//echo microtime(true) . " $ttl";
	while ($r === 1 && microtime(true) < $ttl){
		usleep(10000);
		exec('mkdir -m 777 locked',$res,$r); 
	}

	//sleep(3);
	//exec('rm -R locked');
	echo "Start time: " . $_SERVER['REQUEST_TIME'] . " Finish time: " . microtime(true);*/
?>