<?php
	$pagesList = array(1,2,3,4,5,6,7,8,9);
	function array_two_direction($pagesList) {
		$twoDirectionList = array();
		$iterator1 = 0;						//starts at 0
		$iterator2 = count($pagesList)-1;   //start at last index

		for( $i = 0; $i < count($pagesList)/2; $i++ ) {		//iterate thru half the original array

			array_push( $twoDirectionList, $pagesList[$iterator1++] );	//push forward direction element in

			/*
			* Check if new array length is < original length, this
			* effectively will prevent double push of the same element
			*/
			if(count($twoDirectionList) < count($pagesList)) {			
				array_push($twoDirectionList,  $pagesList[$iterator2--] );
			}
		}

		return $twoDirectionList;
	}

	print_r(array_two_direction($pagesList) );
?>