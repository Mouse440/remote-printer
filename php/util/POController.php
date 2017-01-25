<?php
/*
* This class handles Print Options validatation  
*/
class POController {
	
	/*
	* This function is used in phase2
	* It validates all data sent from printing options
	* @param data - $_POST super array
	* @param amount - string document amount
	*/
	public static function validateData($data) {
		
		//dirty way to use clean amount
		// $data['amount'] = $amount;

		//Validate copies 
		if( !preg_match('/[1-9]{1,3}/', $data["copies"], $result) )  { //report error
			throw new Exception("Invalid copies input format.");
		}

		//Validate page range
		self::validatePageRange($data["range"],$data["amount"]);

		//Validate twoSided 
		if( !preg_match('/true|false/', $data["twoSided"]) ) {
			throw new Exception("Malicious boolean variable.");
		}

		//Validate layout
		self::validatePageLayout($data["layout"]);

		
		//Validate allowance
		// if( intval($data['allowance']) !== intval(AllowanceController::getUserAllowance($_SESSION['_USER_']["MemberID"])) ) {
		// 	throw new Exception("Mismatch allowance.");		
		// }

		// //Validate file 
		// if( $_SESSION['signature'] !== FileUtilities::getSignature($_FILES["file"]["tmp_name"])) {
		// 	throw new Exception("Mismatch signature.");
		// }

		//Validate original pages amount
		// if( intVal( FileUtilities::getPageAmount($_FILES["file"]["tmp_name"]) ) != intVal($data["amount"]) ) {
		// 	throw new Exception("Mismatch original pages amount.");
		// }

		//Unset signature session variable
		// unset($_SESSION['signature']);

		//Validate total
		if( intVal($data["total"]) !== self::getTotal($data) ) {
			throw new Exception("Mismatch total.");
		}

	}

	/*
	* Check if page layout option is valid
	* @layout - page layout
	* @exception - Unexpected layout
	*/
	public static function validatePageLayout($layout) {
		if( !preg_match('/portrait|landscape/', $layout) ) {
			throw new Exception("Unexpected layout $layout.");
		}
	}

	/*
	* check page range for proper format
	* @param range - formated printing range
	* @param amount - original document page amount
	* @return string - the original $range or new range with last page as the largest page number of doc
	*/
	public static function validatePageRange($range,$pageAmount) {
		$data["range"] = strval( $range );
		$data["amount"] = $pageAmount;

		if( strlen($data["range"]) === 0) { //special case when text field is empty
			throw new Exception("Range is empty.");
		}

		if( preg_match('/[^1-9]/', $data["range"][0]) || //check for 0 in the first character
				!preg_match('/[0-9]/', $data["range"][strlen($data["range"]) - 1]) ) { //check for non digit in last character  
			throw new Exception("Invalid range.");
		}


		if(preg_match('/[^0-9\-\,]/', $data["range"])) { //check for invalid characters
			throw new Exception("Invalid character found.");  
		}

		//check for valid internal range
 		if ( preg_match('/[\,\-]+/', $data["range"]) ){ //special cass 2-3-4
			$noCommaArr = preg_split('/\,/' , $data["range"]);
			foreach ($noCommaArr as $str) {
				if ( count( preg_split('/\-/', $str)) > 2 && strpos($str, '-') ) {
					throw new Exception("Double '-' found.");
				}
			}
		} 	

		//continue with validation, at this stage the string should contain only number and/or  ',' and/or '-'
		$numArray = preg_split('/[\-\,]/', $data["range"]); 

		//check if $numArray only has 1 number
		if( count($numArray) == 1 ) { 					
			if( intval($numArray[0]) > intVal($data["amount"]) ) {		//check if the single page is within the max page number
				throw new Exception("Page is out of range.");
			} 
		} else {									//$numArray has more than 1 number
			$lastNum = $numArray[count($numArray)-1];
			$lastNumPos = strpos($data["range"], $lastNum);
			$leftCharOfLastNum = substr($data["range"], $lastNumPos-1, 1);

			/*if($leftCharOfLastNum == ',' || intVal($lastNum) > intVal($data["amount"]) ) { //checking if last number is a page outside of range of number of pages
				throw new Exception("Page is not in range."); 
			}*/ 

			if(intVal($lastNum) > intVal($data["amount"]) ) { //checking if last number is a page outside of range of number of pages
				throw new Exception("Page is not in range."); 
			} else {
				for($i=1; $i<count($numArray); $i++) { //checking for uniform smaller to larger values
					$prevNum = $numArray[$i-1];
					$currentNum = $numArray[$i];
					if( $prevNum >= $currentNum || $prevNum > $data["amount"] ) {
						throw new Exception("Range is not in order.");
						break;
					}
				}
			}	
		}
	}

	/*
	* Caculate and return the total pages of print order
	* @return int value of total pages
	* NOTE: At this stage, all the validation has been checked and it is safe to assume that $data['range'] is in valid format
	*/
	public static function getTotal($data) {
		$totalPages = 0;	//init
		$numArray = preg_split('/[\-\,]/', $data["range"]); //desaturate range of "," and "-"
		if(count($numArray) !== 1) { //algorithm for more than 1 pages
			$array = preg_split('/\,/', $data["range"]);
			foreach($array as $node) {	
				$pageNum = preg_split('/\-/', $node); //checking if node has - 
				if( count($pageNum) !== 1 ){
					$leftNum = $pageNum[0];
					$rightNum = (intVal($pageNum[1]) > intVal($data["amount"]) ) ? intVal($data["amount"]) : intVal($pageNum[1]);

					$totalPages += $rightNum - $leftNum + 1;
				} else { //node is a single number
					$totalPages += 1;
				}	
			}
		} else { //algorithm for a single page
			$num = $numArray[0];
			if( intVal($num) <= intVal($data["amount"]) ) {
				$totalPages = 1;
			} else {
				throw new Exception("Invalid range $num");
			}
		}
		return $totalPages*intVal($data['copies']);
	}
}

?>