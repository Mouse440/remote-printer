<?php

require_once(__DIR__.'/../../gcp/GoogleCloudPrint.php');
require_once(__DIR__.'/../config/Config.php');


class GCPPrintAdapter {

	private $gcp;
	private $gcpConfig;
	private $filePath;
	private $printerId;
	private $filePrefix;
	private $copies;
	private $urlconfig = array(	
        'authorization_url' 	=> 'https://accounts.google.com/o/oauth2/auth',
        'accesstoken_url'   	=> 'https://accounts.google.com/o/oauth2/token',
        'refreshtoken_url'      => 'https://www.googleapis.com/oauth2/v3/token'
    );

	/*
	* Constructor overloading
	* @param formData data array
	* @param gcp - google cloud printer object
	* @param gcpConfig - google cloud printer configuration array
	*/
	public function __construct($formData) {	

		$this->gcp = new GoogleCloudPrint();		
		$this->gcpConfig = Config::$gcpConfig;		//gcp configuration
        $this->filePath = Config::getPrinterAppRoot().'/'.$formData['fileToPrintFullPath']; //full system path of the file location
        $this->printerId = Config::$gcpPrinterId;			//unique pritner id
        $this->filePrefix = $formData['filePrefix'];		//file name
		$this->result = array(
                           'jobId' => 'Google Cloud Print',	//fake jobid
                           'command' => 'Google Cloud Print'	//fake command
                           );
		$this->copies = $formData['copies'];		//get the copies

	}

	/*
	* Execute the print via gcp service
	* @return true/error message - success boolean or error message and code 
	*/
	public function executePrint(){
		//query for an access token using the refresh token
		$token = $this->gcp->getAccessTokenByRefreshToken(   
										$this->urlconfig['refreshtoken_url'],
											http_build_query($this->gcpConfig) 
		);
		$this->gcp->setAuthToken($token); //set the new access token

		for($i = 1; $i <= $this->copies; $i++ ) {
			//send the print job 
			$resarray = $this->gcp->sendPrintToPrinter( 
				$this->printerId,
				 "copy $i-".$this->filePrefix, 
				 $this->filePath, 
				 "application/pdf");
			if($resarray['status'] === true) {
				continue;
			} else {
				return "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
			}
		}
		return true;
		
	}	

	/*
	* Return the result for saving to db
	*/
	public function getResult() {
		return $this->result;
	}
}


