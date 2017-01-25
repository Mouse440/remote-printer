<?php
	define('DEMO_MODE',TRUE); //demo mode
	define('OUT_OF_SERVICE', FALSE);//out of service cons`tant, user when system is out of service
	define('DEV_MODE', FALSE);    // this constant signal dev mode, for production, this should be false
	define('SOFFICE_NEEDED', FALSE); // for production, this should be false
	define('DEV_FILE_PATH', __DIR__.'/php/phase1/dev.php');  // path to dev file
	define('DEMO_FILE_PATH', __DIR__.'/php/phase1/demo_setup.php');  // path to dev file
	require_once(__DIR__.'/php/util/show-error.php');
	require_once(__DIR__.'/php/security/checkLoginV3.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>SCE Printing Service</title>
	<!-- mobile friend option -->
	<meta name="viewport" content="width=device-width">

	<!--bootstrap core-->
	<link rel="stylesheet" type="text/css" href="css/bootstrap/css/bootstrap.css"></li>

	<!--additional css-->
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="css/spinner/three-quarters.css" type="text/css">

	<!-- Js libraries-->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
	<script type="text/javascript" src="js/util/jQuery.typing-0.2.0.min.js"></script>
	<script type="text/javascript" src="js/util/bootbox.min.js"></script>
	<!-- // <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script> -->

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-71601789-1', 'auto');
	  ga('send', 'pageview');

	</script>

	<meta charset="UTF-8"> 
</head>
<body >
	<div id="container" class="container">
		<div >
			<div id="welcome_header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<label >
						<h2>
								Welcome to <br>
							SCE Printing Service <br>
								Ver.3 Beta
						</h2>
				</label> 
			</div>

			<!-- <div class="label label-danger"></div> <h5 class="help_text">
						Begin by uploading a file:
					</h5> -->
			<div style="display:none;"id="upload_menu" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="col-md-6 col-md-offset-3">
					<ul class="nav nav-pills nav-justified">
						<li class="active">
							<a href="">Local Upload</a>
						</li>
						<li class="disabled" title="Coming soon!">
							<a title="Coming soon!" href="javascript:void(0)">By URL</a>
						</li>
					</ul>
				</div>
			</div> <!--//end upload_menu-->

			<!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<hr id="upload_menu_separator">
			</div> -->


			<div id="local_upload" class="upload_space col-xs-12 col-sm-12 col-md-12 col-lg-12">

				<!-- error_space -->
				<div id="local_upload_error_display" class="warning_space alert alert-danger col-xs-12 col-sm-12 col-md-12 col-lg-12" role="alert">
				  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				  <span class="sr-only">Error:</span>
				  <span id="error_msg"></span>
				</div><!--//end error_space -->
				

				<form id="form" class="form" name="form" method="POST"> <!--enctype="application/pdf" -->
					<a href="#local_upload">
						<!-- Upload Zone -->	
						<div id="add_file_space" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
							<label>
								<h4>Drag & Drop </h4>
								<h4> or Touch Here</h4>
								<img id="upload_image" src="images/cloud-icon_3.png">
							</label>
						</div>
					</a>
					<div id="restriction_information" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
						<label class='max-size-label'>
							Suppported pdf only.<br>
							Limit 10MB per transaction
						</label>
					</div>

					<!--  -->
					<input id="file_input_field" type="file" name="file" accept="application/pdf">

					<!-- Uploaded File Display -->
					<div id="uploaded_file_space" class="row col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
						<div id="file_name" class='uploaded_file_name panel col-xs-9 col-sm-9 col-md-10 col-lg-10 pull-left' name='label'>
							<!-- <div  class="">
							</div> -->

							
						</div>
						<div class='delete-icon col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-1 col-sm-offset-1 col-md-offset-0 col-lg-offset-0'>
							<a class="pull-right" href="javascript:void(0)">Remove</a>
						</div>
					</div>

					<!-- <hr> -->
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xs-offset-0 col-sm-offset-0 col-md-offset-0 col-lg-offset-0">
						<img id ="ajax-loader1" src="images/ajax-loaderv3.gif" >
						<br>	
						<button id="continue_btn" type="button" class="btn btn-default btn-lg active" disabled>Continue</button>
						<br>
						<br>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
	  	
	  	<!-- Modal Content -->
	    <div class="modal-content">
	    	<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Confirmation Form</h4>
		    </div>
	    	<!-- Modal Body -->
		    <div class="modal-body">
		    	<div class="row">
		    	<!-- Preview Space -->
		    	<div id="preview-space" class="col-md-8 col-lg-9" >
		    		<div id="preview-loader-animation" class="three-quarters-loader">
					  Loading Preview…
					</div>
		    		<div id="preview-pdf" >
		    			<!-- <img class="center-target" src="images/ajax-loader-black-indicator.gif"/> -->
		    			<embed class="preview-plugin hidden-xs" type="application/pdf">
  						</embed>

  						<!-- <iframe class="preview-plugin hidden-sm hidden-md hidden-lg" width="100%" height="450px" type="application/pdf">
  						</iframe> -->
		    		</div>
		    		<!-- <div id="preview-carousel" class="carousel slide" data-ride="carousel" data-interval="false">
				  		<!-- Indicators -->
						<!-- <ol class="carousel-indicators">
						    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
						    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
						    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
						</ol> -->

						<!-- Wrapper for slides 
						<div class="carousel-inner" role="listbox">
						    <!-- <div class="item active">
						        <img src="temporary_file_storage/doc1/1.png" alt="...">
						        <div class="carousel-caption">
						        	1 of 9
						    	</div>
						    </div>
						    1 of 9 
						</div>

						<!-- Preview Pages Caption 
						<div id="preview-pages-caption"></div>

					  	<!-- Controls 
					  	<a class="left carousel-control" href="#preview-carousel" role="button" data-slide="prev">
					    	<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						    <span class="sr-only">Previous</span>
					  	</a>
					  	<a class="right carousel-control" href="#preview-carousel" role="button" data-slide="next">
					    	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					    	<span class="sr-only">Next</span>
					  	</a>

					  	<script id="preview-images-template" type="text">
					  		<div class="item">
						        <img class="preview-image-src" src="" alt="" page-num"">
						    </div>
					  	</script>
					</div> -->
	    		</div>

		      	<!-- Print Options Space -->
    			<div id="print-options" class="col-md-4 col-lg-3">
    				<!-- Form -->
	    			<form id="confirm-form" class="form-horizontal row" name="confirmation-form" method="POST">

			      		<!-- Total Pages Section -->
			      		<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
			      			<div class="option-title col-xs-4 col-sm-4 col-md-3 col-lg-3"> Total: </div> 
			      				<div id="total" class="receipt-value amount-values col-xs-8 col-sm-8 col-md-9 col-lg-9" ></div>
			      				<!-- <div id="total-description" class="receipt-value right-options col-xs-8 col-sm-8 col-md-6 col-lg-6"></div> -->

			      			<div class="option-title col-xs-4 col-sm-4 col-md-3 col-lg-3"> Allowed: </div> 
			      				<div id="allowance" class="receipt-value amount-values col-xs-8 col-sm-8 col-md-9 col-lg-9"></div> 
			      				<!-- <div id="allowance-description" class="receipt-value right-options col-xs-8 col-sm-8 col-md-6 col-lg-6"></div> -->
			      		</div> <!-- end Total Pages Section -->
 
			      		<div class="form-group hr col-xs-12 col-sm-12 col-md-12 col-lg-12">
		      				<hr color="gray">
		      			</div>

		      			<!-- Copies Quantity Section -->
			      		<div id="copies-space" class="form-group printing-option-space col-xs-12 col-sm-12 col-md-12 col-lg-12">
			      			<div class="option-title col-xs-4 col-sm-4 col-md-3 col-lg-3"> Copies: </div> 
			      			<div class="receipt-value col-xs-8 col-sm-4 col-sm-offset-8 col-md-6 col-lg-6 col-xs-offset-0 col-md-offset-3 col-lg-offset-3" >
				      			<div id="copies-div" class="error-text" data-toggle="popover" data-placement="right" data-content="Use a number"> 
	      							<div class="input-group col-xs-8 col-sm-8 col-md-12 col-lg-12">
	      								<input id="copies-amount" type="text" class="form-control" value="1" maxlength="3">
				      					<span class="input-group-btn">
				      						<button id="add-copy-button" type="button" class="btn btn-default active incr-btn" value=true> + </button>
				      						<button id="subtract-copy-button" type="button" class="btn btn-default active incr-btn" value=false> - </button> 
				      					</span>
			      					</div>
				      			</div>
				      		</div>
			      		</div> <!-- end Copies Quantity Section -->

			      		<!-- Pages Section -->
			      		<div id="pages-space" class="form-group printing-option-space col-xs-12 col-sm-12 col-md-12 col-lg-12">
			      			<div class="option-title col-xs-4 col-sm-4 col-md-3 col-lg-3"> Pages: </div> 
			      			<div id="pages" class="receipt-value col-xs-8 col-sm-4 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-4 col-md-offset-3 col-lg-offset-3" >
	      						<div id="pages-options" data-toggle="popover" data-placement="right" data-content="Invalid page range, use e.g. 1-5, 8, 11-15">
		      						<label>
		      							<input id="radio-all"  type="radio" name="pages" checked>&nbsp All 
		      						</label>
		      						<br>
		      						<span class="display-inline">
			      						<input id="radio-other" class="friend" type="radio" name="pages" >&nbsp
			      						<input id="page-range" class="friend form-control" type="text" name="page-range"  placeholder="1-5, 8, 11-15" >
		      						</span>
		      					
		      						<div id="pages-options-error" class="col-xs-12 col-sm-12 col-md-offset-2 col-md-10 col-lg-12">
		      							Invalid page range, use e.g. 1-5,6,8-9
		      						</div>
		      					</div> 
		      				</div>
			      		</div> <!-- end Pages Section -->

			      		<!-- Layout Section -->
			      		<div id="layout-space" class="form-group printing-option-space col-xs-12 col-sm-12 col-md-12 col-lg-12">
			      			<div class="option-title col-xs-4 col-sm-4 col-md-3 col-lg-3"> Layout: </div> 
			      			<div id="layout-div" class="receipt-value col-xs-8 col-sm-4 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-4 col-md-offset-3 col-lg-offset-3" >
			      					<label>
			      						<input id="radio-portrait" type="radio" name="layout" value="portrait" checked> Portrait 
			      					</label>
		      						<br>
			      					<label>
			      						<input id="radio-landscape" type="radio" name="layout" value="landscape"> Landscape
			      					</label>
			      			</div>
			      		</div> <!-- end Layout Section -->

			      		<!-- Extra options section -->
			      		<div class="form-group printing-option-space col-xs-12 col-sm-12 col-md-12 col-lg-12">
			      			<div class="option-title col-xs-4 col-sm-4 col-md-3 col-lg-3"> Options: </div> 
			      			<div id="two-sided-div" class="receipt-value col-xs-8 col-sm-4 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-4 col-md-offset-3 col-lg-offset-3" > 
		      					<label>
		      						<input id="two-sided-box" type="checkbox" name="two-sided" checked>&nbsp Two-sided 
		      					</label>
		      				</div> 	
			      		</div> <!-- end Extra options section -->
			      		

		      			<!-- <div class="form-group hr col-xs-12 col-sm-12 col-md-12 col-lg-12">
		      				<hr color="gray">
		      			</div> -->

		      			<!-- <hr color="gray"> -->
	      		 	</form> 
      		 	</div> <!-- end Print Options -->

      		 	<!-- Loading Gif -->
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
	      			<img id="modal-processing-div" src="images/ajax-loaderv3.gif"></br>
	      			<span id="jobStatus"></span>
	      		</div>
      		 </div> <!-- end row -->
			</div> <!-- end Modal Body -->

			<!-- Modal Footer -->
	      	<div class="modal-footer">
	      		
	        	<button id="cancelBtn" type="button" class="btn btn-default btn-lg" data-dismiss="modal" >Cancel</button>
	        	<button id="printBtn" type="button" class="btn btn-primary btn-lg"  >Print</button>
	      	</div> <!-- end Modal Footer -->
	    </div> <!-- end modal-content -->
	  </div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">

	    	<form id="alert-form" name="alert-form" method="POST"> <!-- id changed from result-form to alert-form -->

		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h3 class="modal-title" id="alert-header"></h3> <!-- id changed from resultLabel to alert-header -->
		      </div>

		      <div class="modal-body" id="alert-body"> <!-- id changed from result-body to alert-body -->
		     
		     	</div> 		
		      <div class="modal-footer">
		        <button id='noPermissionBtn'type="button" class="btn btn-default permission-btn" data-dismiss="modal">No, I prefer not.</button>
		        <button id="permissionBtn" type="button" class="btn btn-primary permission-btn" data-dismiss="modal">Sure! go for it.</button>
		        <button id="resultPrimaryBtn" type="button" class="btn btn-primary" data-dismiss="modal" >Close</button>
		      </div>
		    </form> 

	    </div>
	  </div>
	</div>
</body>
	<script type="text/javascript" src="js/layers/ObserverEngine.js"></script>
	<script type="text/javascript" src="js/layers/Transaction.js"></script>
	<script type="text/javascript" src="js/layers/upload_zone.js"></script>
	<script type="text/javascript" src="js/layers/uploaded_file_display.js"></script>
	<script type="text/javascript" src="js/layers/pdf_preview_controller.js"></script>
	<script type="text/javascript" src="js/layers/upload_error_display.js"></script>
	<script type="text/javascript" src="js/layers/continue_button.js"></script>
	<script type="text/javascript" src="js/layers/copies_panel.js"></script>
	<script type="text/javascript" src="js/layers/preview_display.js"></script>
	<script type="text/javascript" src="js/layers/pagerange_panel.js"></script>
	<script type="text/javascript" src="js/layers/total_panel.js"></script>
	<script type="text/javascript" src="js/layers/allowance_panel.js"></script>
	<script type="text/javascript" src="js/layers/layout_panel.js"></script>
	<script type="text/javascript" src="js/layers/two_sided_option.js"></script>
	<script type="text/javascript" src="js/layers/print_button.js"></script>
	<script type="text/javascript" src="js/layers/print_status_panel.js"></script>


	<!-- <script type="text/javascript" src="js/layers/upload_form.js"></script> 
	<script type="text/javascript" src="js/layers/front_page.js"></script>
	<script type="text/javascript" src="js/layers/confirm_page.js"></script>
	<script type="text/javascript" src="js/layers/miscellaneous.js"></script>
	<script type="text/javascript" src="js/layers/confirmation_form.js"></script>
	-->
	<script type="text/javascript" src="js/layers/main.js"></script>

</html>