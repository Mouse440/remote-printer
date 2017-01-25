<?php
	require_once(__DIR__."/../config/Config.php");
	require_once(__DIR__."/URLDownloaders/DefaultURLDownloader.php");

	/*
	* This is an abstract class for reading file from URL
	*/
	Class URLDownloaderStrategy {
		private $downloader;
		private $url;
		private $extensions;
		private $fileName; 
		
		/*
		* Contructor
		*/
		public function __construct($url) {
			$this->url = $url;
		    $this->extensions = Config::$extensions;
		    
		    switch($dlType = $this->validateUrl()) {
		    	case 0: //Default downloader
		    		echo "default";
		    		$this->downloader = new DefaultURLDownloader($url);
		    		break;
		    	case 1: //Google Drive downloader
		    		echo "google drive";
		    		break;
		    	default: 
		    		throw new Exception("Invalid URL: $url");
		    }
		}
		
		/*
		* Validate URL and get its type
		*/
		private function validateUrl(){
			$u = $this->url;
			$rawExt = end(explode(".",$u));

		 	if( in_array($rawExt, array_keys($this->extensions)) ) {
		 		return 0;
		 	} else {
		 		return -1;
		 	}
		}

		public function downloadFile() {
			return $this->downloader->downloadFile();
		}
	}