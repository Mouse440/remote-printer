<?php
	require_once(__DIR__.'/../php/config/Config.php');
	require_once(__DIR__.'/../php/util/FileUtilities.php');

	//FileUtilities::unlinkFile('../'.Config::$tempFileStorageName,'ee2935ee7bc6be7399257473cde0967fb530bc7d.pdf');
	// print_r( FileUtilities::getInterestedFilesAndFolders(Config::getFileStoragePath(),"70600e44c35e478ace6041f3064692448ad9b0e2.1433986055.05") );
	FileUtilities::unlinkAllWithPrefix(Config::getSpoolDirPath(),'3a7b7a519f192122153c4728f29835c4aac6f3db.1435799326.894');
	// FileUtilities::unlinkAllWithPrefix('testdocs/');


?>