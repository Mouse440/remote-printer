<?php
	define('OUT_OF_SERVICE', FALSE);//out of service cons`tant, user when system is out of service
	define('DEV_MODE', TRUE);    // this constant signal dev mode, for production, this should be false
	define('SOFFICE_NEEDED', FALSE); // for production, this should be false
	define('DEMO_MODE',FALSE); //demo mode
	
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
	<!-- <link rel="stylesheet" href="css/spinner/three-quarters.css" type="text/css"> -->

	<!-- Js libraries-->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>

	<script type="text/javascript" src="js/angular/angular.min.js"></script>
	<!-- shim is needed to support non-HTML5 FormData browsers (IE8-9)-->
	<script type="text/javascript" src="js/angular/ng-file-upload/ng-file-upload-all.min.js"></script>
	<script type="text/javascript" src="js/angular/ng-file-upload/ng-file-upload.min.js"></script>
	<script type="text/javascript" src="js/angular/angular-modal-service.min.js"></script>

	<!-- <script type="text/javascript" src="js/util/jQuery.typing-0.2.0.min.js"></script> -->
	<!-- <script type="text/javascript" src="js/util/bootbox.min.js"></script> -->
	<!-- // <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script> -->
	<meta charset="UTF-8"> 
</head>
<body >
	<div>
		<!-- <div class="label label-danger"></div> <h5 class="help_text">
					Begin by uploading a file:
				</h5> -->
		<div style="display:none;" id="upload_menu" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
	</div>
	<div id="container" class="container" ng-app='remotePrinter' ng-controller="AppCtrl as app" >
		<div style="margin-top:5px;" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><a href="../">Return to main page</a></div>
		<div id="welcome_header" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label>
					<h2>
							Welcome to <br>
						SCE Printing Service
					</h2>
			</label> 
		</div>

		<div ng-controller='UploadCtrl as uploader' >
			<form name="uploadForm">
				<!-- error_space -->
				<div ng-cloak ng-show="uploadForm.file.$error.maxSize || uploadForm.file.$error.pattern || uploader.error" id="local_upload_error_display" class="warning_space alert alert-danger col-xs-12 col-sm-12 col-md-12 col-lg-12" role="alert">
				    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				    <span class="sr-only">Error:</span>
				    <span ng-show="uploadForm.file.$error.pattern">File does not match allowed format</span>
				    <span ng-show="uploadForm.file.$error.maxSize">File must be less than 10MB</span>
				    <span ng-show="uploader.error">{{uploader.error}}</span>
				</div><!--//end error_space -->

				<div ngf-drop ngf-select ng-model="uploader.file" id="add_file_space" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2" 
				        ngf-drag-over-class="'dragover'" 
				        ngf-multiple="false" 
				        ngf-allow-dir="false"
				        ngf-max-size='10MB'
				        accept="image/*,application/pdf" 
				        ngf-pattern="'image/*,application/pdf'"
				        name="file">
				        <label>
							<h4>Drag & Drop </h4>
							<h4> or Touch Here</h4>
							<img id="upload_image" src="images/cloud-icon_3.png">
						</label>
				</div>
			</form>
			<div id="restriction_information" class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
				<label class='max-size-label'>
					<!-- , odt, doc/x, ppt/x, jpg/jpeg, png. -->
					Suppported images/pdf only.<br> 
					Limit 10MB per transaction
				</label>
			</div>
			
			<div ng-cloak ng-if="uploader.progressPercentage > -1" class="progress col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
				<div class="progress-bar progress-bar-striped active" role="progressbar"
				  aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" ng-style="{width: uploader.progress}">
				    {{uploader.progress}}
			    </div>
			</div>

			<!-- Uploaded File Display -->
			<div ng-cloak ng-if="uploader.filename" id="uploaded_file_space" class="row col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
				<div id="file_name" class='uploaded_file_name panel col-xs-9 col-sm-9 col-md-10 col-lg-10 pull-left' name='label'>
					{{uploader.filename}}
				</div>
				<div class='delete-icon col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-1 col-sm-offset-1 col-md-offset-0 col-lg-offset-0'>
					<a class="pull-right" href="javascript:void(0)" ng-click="uploader.clearFile()">Remove</a>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xs-offset-0 col-sm-offset-0 col-md-offset-0 col-lg-offset-0">
			<img id ="ajax-loader1" src="images/ajax-loaderv3.gif" >
			<br>	
			<button id="continue_btn" type="button" class="btn btn-default btn-lg active" ng-click="app.showPrintOptions()" ng-disabled="!app.page_amount">Continue</button>
			<br>
			<br>
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
		        <button id='noPermissionBtn' type="button" class="btn btn-default permission-btn" data-dismiss="modal">No, I prefer not.</button>
		        <button id="permissionBtn" type="button" class="btn btn-primary permission-btn" data-dismiss="modal">Sure! go for it.</button>
		        <button id="resultPrimaryBtn" type="button" class="btn btn-primary" data-dismiss="modal" >Close</button>
		      </div>
		    </form> 

	    </div>
	  </div>
	</div>
</body>
	<!-- <script type="text/javascript" src="js/layers/ObserverEngine.js"></script>
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
	<script type="text/javascript" src="js/layers/print_status_panel.js"></script> -->


	<!-- <script type="text/javascript" src="js/layers/upload_form.js"></script> 
	<script type="text/javascript" src="js/layers/front_page.js"></script>
	<script type="text/javascript" src="js/layers/confirm_page.js"></script>
	<script type="text/javascript" src="js/layers/miscellaneous.js"></script>
	<script type="text/javascript" src="js/layers/confirmation_form.js"></script>
	-->
	<!-- <script type="text/javascript" src="js/layers/main.js"></script> -->
	<script type="text/javascript" src="js/app.js"></script>
</html>