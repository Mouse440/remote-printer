<?php
	define('DEV_MODE', TRUE);    // this constant signal dev mode, for production, this should be false
	define('SOFFICE_NEEDED', TRUE); // for production, this should be false
	define('DEV_FILE_PATH', __DIR__.'/php/phase1/dev.php');  // path to dev file

	require_once( __DIR__.'/php/security/checkLoginV3.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>SCE Printing Service</title>
	<!--bootstrap core-->
	<link rel="stylesheet" type="text/css" href="css/bootstrap/css/bootstrap.css"></li>

	<!--additional css-->
	<link rel="stylesheet" type="text/css" href="css/style.css">

	<!--additional scripts-->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
	<script type="text/javascript" src="js/util/jQuery.typing-0.2.0.min.js"></script>

	
	<script type="text/javascript" src="js/layers/front_page.js"></script>
	<script type="text/javascript" src="js/layers/confirm_page.js"></script>
	<script type="text/javascript" src="js/layers/miscellaneous.js"></script>
	<script type="text/javascript" src="js/layers/main.js"></script>


	<!-- // <script type="text/javascript" src="js/security/check-user.js"></script> -->
	<meta charset="UTF-8"> 
</head>
<body>
	<div id="container" class="container">
		<div class="col-xs-12">
			<div class="col-xs-12">
				<label>
					<h2>
						Welcome to <br>
						SCE Printing Service
					</h2>
				</label> 
			</div>

			<!-- <div id="warning_space" class="label label-danger"></div> -->
			
			<form id="form" name="form" method="POST" class="col-xs-12"> <!--enctype="application/pdf" -->
				<div id="add_file_space">
					<label>
						<h4>Drop or Touch Here</h4>
						<img id="upload_image" src="images/cloud-icon_2.png">
					</label>
				</div>
				<div>
					<label id='max-size-label'>
						PDFs with max size of 10MB only.
					</label>
				</div>

				<!-- accept="application/pdf" -->
				<input id="file_input_field" type="file" name="file" >

				<div id="uploaded_file_space">
					<!-- ##### -->
					<div class='uploaded_file_name panel' name='label'>
						<div id="file_name">
						</div>

						<img src='images/delete-icon.jpg' class='delete-icon' title='Delete this file'>
					</div>

					
				</div>
				<img id ="ajax-loader1" src="images/ajax-loader.gif" >
				<br>	
				<button id="submit_btn" class="btn btn-default btn-lg active" disabled>Submit</button>
				<br>
				<a href="../printing-version1">Having issues? Revert to previous version.</a>
				<br>
			</form>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg ">
	  	
	    <div class="modal-content container">
	    	<div id="preview-space" class="col-md-4" style="border:1px solid red;">
    		</div>
	    	<div>
    		<!-- <div class="col-md-4"> -->
	    	<form id="confirm-form" name="confirmation-form" method="POST">

		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Confirmation Form</h4>
		      </div>

		      <div class="modal-body">
		      		<!-- Total Pages Section -->
		      		<div class="printing-option-space">
		      			<span> Total: </span> <span id="total" class="receipt-value" ></span><br>
		      			<span> Allowed: </span> <span id="allowance" class="receipt-value"></span> 

		      		</div>

		      		<hr color="gray">

		      		<!-- Pages Section -->
		      		<div id="pages-space" class="printing-option-space">
		      			<span> Pages: </span>
	      				<span id="pages">
      						<div id="pages-options" data-toggle="popover" data-placement="right" data-content="Invalid page range, use e.g. 1-5, 8, 11-15">
	      						<input id="radio-all"  type="radio" name="pages" checked>&nbsp All <br>
	      						<span class="display-inline">
		      						<input id="radio-other" class="friend" type="radio" name="pages" >&nbsp
		      						<input id="page-range" class="friend form-control" type="text" name="page-range"  placeholder="e.g. 1-5, 8, 11-15" >
	      						</span>
	      					
	      						<div id="pages-options-error" class="">
	      							Invalid page range, use e.g. 1-5,6,8-9
	      						</div>
	      					</div> 

	      				</span>
		      		</div>

		      		<hr color="gray">

		      		<!-- Copies Quantity Section -->
		      		<div id="copies-space" class="printing-option-space row">
		      			<span> Copies: </span>	
		      			<span>
			      			<div id="copies-div" class="col-md-3 error-text" data-toggle="popover" data-placement="right" data-content="Use a number"> 
      							<div class="input-group">
      								<input id="copies-amount" type="text" class="form-control" value="1" maxlength="3">
			      					<span class="input-group-btn">
			      						<button id="add-copy-button" type="button" class="btn btn-default active incr-btn" value=true> + </button>
			      						<button id="subtract-copy-button" type="button" class="btn btn-default active incr-btn" value=false> - </button> 
			      					</span>
		      					</div>
			      			</div>

			      			<div id="two-sided-div"> 
		      					<input id="two-sided-box" type="checkbox" name="two-sided" checked>&nbsp Two-sided 
		      				</div> 
			      		</span>
		      		</div>

		      		<hr color="gray">

		      		<!-- Layout Section -->
		      		<div id="layout-space" class="printing-option-space">
		      			<span> Layout: </span>
		      			<span>
		      				<div id="layout-div">
		      					<input id="radio-portrait" type="radio" name="layout" value="portrait" checked> Portrait <br>
		      					<input id="radio-landscape" type="radio" name="layout" value="landscape"> Landscape
		      				</div>
		      			</span>
		      		</div>
		      </div>

		      <div class="modal-footer">
		      	<div id="modal-processing-div">
		      		<img src="images/ajax-loader.gif"></br><span id="jobStatus">Processing...</span>
		      	</div>
		        <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelBtn">Cancel</button>
		        <button type="button" class="btn btn-primary" id="printBtn" >Print</button>
		      </div>

		    </form> 
		</div>
			<!-- </div>	 -->
	    </div>
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
	<footer id='footer' class='footer custom-footer'>
		<div class="footer-content">
		  <button id="comm-feedback-btn" type="button" class='btn btn-default btn-sm' data-toggle="modal" data-target="#feedbackModal" data-suggestion="What could we do to improve our application?">
		  		<div class="glyphicon glyphicon-comment custom_color"></div> Comment/Suggestions 
		  </button> 
		  <button id="err-feedback-btn" type="button" class='btn btn-default btn-sm' data-toggle="modal" data-target="#feedbackModal" data-suggestion="What was the error? Decribe the steps that got you there:">
		  		<div class="glyphicon glyphicon-warning-sign custom_color"></div> Report An Error 
		  </button>

		  <div> created by <a id="credit" href="https://www.linkedin.com/in/duydnguyen07" target="_blank">Duy Nguyen</a></div>
		</div>

		<!-- Feedback Modal -->
		<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="feedbackModalLabel">Feedback</h4>
		      </div>
		      <div class="modal-body">
		      	<div id="feedback-error-msg" class="alert alert-danger" role="alert" ><h4>Message cannot be empty!</h4></div>
		        <form>
		          <div class="form-group">
		            <label id="feedback-suggestion-label" for="message-text" class="control-label"></label>
		            <textarea id="message-text" maxlength="512" class="form-control" ></textarea>
		          </div>
		        </form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <button id="feedback-submit-btn" type="button" class="btn btn-primary">Send message</button>
		      </div>
		    </div>
		  </div>
		</div>
	</footer>
</body>

</html>