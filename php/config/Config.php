<?php
/*
* This class holds all the configuration parameter needed for the Remote Printer app
  
	--- SPECIAL NOTES --- 
   * Store file in storage directory
   * Note: Inorder for file saving to work, the file to upload to must have the right permission -6- and group www-data (_www for mac)
   * Hint: run the following command: chmod 765 temporary_file_storage/ & chown www-data:www-data temporary_file_storage/
*/

class Config {

	//Database credential
	private static $host = "localhost";
	private static $dbuser = "Printer"; 
	private static $dbpass = "@sce123";

	//Public variables
	public static $version = '3'; //application version number, used for db
	public static $previewExtension = 'pdf'; //preview images extensions, png or pdf
	public static $previewImageWidth = 350; //DEPRECATED in pixel 
	public static $previewImageResolution = 130; //DEPRECATED in pixels/inch
	//public static $previewFolderSubfix = 'preview'; //preview folder subfix, this string will be at the end of all preview folders
	public static $previewFileSubfix = 'preview'; //preview folder subfix, this string will be at the end of all preview folders
	public static $maxPreviewConversionAtATime = 3;  //DEPRECATED number of preview picture conversion per request, n
	public static $maxSpoolTimer = 7; //file conversion spool timer in seconds
	public static $encryption_key = 'SECRET'; //deprecated in version3 - Key for signing file 
	public static $reset_date = 'Saturday'; //day of the week to reset 
	public static $reset_amount = array(
									"regularMember" => 30,
									"officer" => 1000
									);

    public static $role_reset_amount = array (
       "administrator" => 200,
       "officer" => 200,
       "editor" => 200,
       "executive" => 1000,
       "subscriber" => 30,
       "pending_user" => 0,
       "alumni" => 0
	);									
	public static $gcpConfig = array(   //google cloud printer config
        'refresh_token' => 'SECRET',
        'client_id' => 'SECRET',
        'client_secret' => 'SECRET',
        'grant_type' => "refresh_token",
	);

	/*
	* Google cloud printer id 
	* To get this Printer ID open /gcp/cron.php on the browser
	*/
	public static $gcpPrinterId = 'SECRET';  //  Brother_MFC_9340CDW <--located in SCE Room ENGR294

	public static $print_states = array( 1 => "pending",
										 2 => "held",
										 3 => "processing",
										 4 => "stopped",
										 5 => "canceled",
										 6 => "aborted",
										 7 => "completed" 
									);
	public static $extensions = array(
	        	'pdf' => 'application/pdf',
	            // 'jpg' => 'image/jpeg',
	            // 'png' => 'image/png',
	            // 'doc' => 'application/msword',
	            // 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	            // 'ppt' => 'application/vnd.ms-powerpoint',
	            // 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	            // 'odt' => 'application/vnd.oasis.opendocument.text',
	            // 'zip' => 'application/zip',//this extension is only used for detecting docx, this is an issue with .docx extension standard
	            //'ps' => 'application/postscript',
	            // 'xls' => 'application/vnd.ms-excel', //not supported
	            // 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' //not supported
	        );  //File extension allowed 

	public static $unconvertableExts = array(			//extension that cannot be converted to pdf
			'zip' => 'application/zip',
			'pdf' => 'application/pdf',
	);

	/*
	* This method return the root directory of the printer app, where index.php lives
	* @return string: print app root dir
	*/
	public static function getPrinterAppRoot() {
		return dirname( dirname(__DIR__) );
	}

	/*
	* This method return the path of the file storage location
	* @return string: file storage path
	*/
	public static function getFileStoragePath() {
		// return dirname(__DIR__).'/temporary_file_storage/';
		return self::getPrinterAppRoot().'/temporary_file_storage/';
	}

	/*
	* The method returns the spool directory path
	* @return string: spool directory path
	*/
	public static function getSpoolDirPath() {
		return self::getPrinterAppRoot()."/spool/";
	}

	/*
	* The method returns the spool setup directory path
	* @return string: spool setup directory path
	*/
	public static function getSpoolSetupDirPath() {
		return self::getPrinterAppRoot()."/spool_setup/";
	}

	/*
	* The method returns the spool setup directory path for 64bit linux machine
	* @return string: spool setup directory path
	*/
	public static function getCpdfPath() { 
		$currentOS = trim( shell_exec('uname') ); //get system operating system

		switch($currentOS) {
			case 'Darwin':
				$cpdfOsFolderName = "OSX-Intel";
			break;
			case 'Linux':	
				$cpdfOsFolderName = "Linux-Intel-64bit";
			break;
			default:	//default case
				$cpdfOsFolderName = "Linux-Intel-64bit";
		}

		return self::getPrinterAppRoot()."/lib/cpdf-binaries-master/$cpdfOsFolderName/cpdf";
	}

	#
	# create a new pdo variable
	#
	public static function getPDO(){
		try {
			return new PDO("mysql:host=" . self::$host . ";dbname=PRINTING;charset=utf8", 
								self::$dbuser, self::$dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::ATTR_PERSISTENT => true) 
						   );
		} catch(PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
    		exit;
		}
	}
}

?>