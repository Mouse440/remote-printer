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
	<link rel="stylesheet" href="assets/css/bootstrap/css/bootstrap.css"></li>

	<!--additional css-->
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/spinner/three-quarters.css" type="text/css">

	<!-- Js libraries-->
	<script type="text/javascript" src="assets/libs/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="assets/libs/bootstrap/bootstrap.min.js"></script>

	<script type="text/javascript" src="assets/libs/angular.min.js"></script>
	<!-- shim is needed to support non-HTML5 FormData browsers (IE8-9)-->
	<script type="text/javascript" src="assets/libs/ng-file-upload/ng-file-upload-all.min.js"></script>
	<script type="text/javascript" src="assets/libs/ng-file-upload/ng-file-upload.min.js"></script>
	<script type="text/javascript" src="assets/libs/angular-modal-service.min.js"></script>

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
							<img id="upload_image" src="assets/img/cloud-icon_3.png">
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
			<uploaded-display></uploaded-display>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xs-offset-0 col-sm-offset-0 col-md-offset-0 col-lg-offset-0">
			<img id ="ajax-loader1" src="assets/img/ajax-loaderv3.gif" >
			<br>	
			<button id="`" type="button" class="btn btn-default btn-lg active" ng-click="app.showPrintOptions()" ng-disabled="!app.options.page_amount">Continue</button>
			<br>
			<br>
		</div>
	</div>

	
</body>
	<script type="text/javascript" src="app/app.js"></script>
	<script type="text/javascript" src="app/shared/endpoints/endpoints.value.js"></script>
	<script type="text/javascript" src="app/shared/endpoints/endpoints.request.service.js"></script>
	<script type="text/javascript" src="app/shared/options.utilities.service.js"></script>
	<script type="text/javascript" src="app/components/execute_print/execute.print.controller.js"></script>
	<script type="text/javascript" src="app/components/uploaded_display/uploaded.display.controller.js"></script>
	<script type="text/javascript" src="app/components/uploaded_display/uploaded.display.directive.js"></script>
	<script type="text/javascript" src="app/components/preview/print.previewer.controller.js"></script>
	<script type="text/javascript" src="app/components/print_options_form/pluralize.filter.js"></script>
	<script type="text/javascript" src="app/components/print_options_form/print.allowance.color.filter.js"></script>
	<script type="text/javascript" src="app/components/print_options_form/print.options.controller.js"></script>
	<script type="text/javascript" src="app/components/copies_panel/copies.panel.directive.js"></script>
	<script type="text/javascript" src="app/components/copies_panel/copies.panel.controller.js"></script>
	<script type="text/javascript" src="app/components/pagerange_panel/pagerange.panel.directive.js"></script>
	<script type="text/javascript" src="app/components/pagerange_panel/pagerange.input.directive.js"></script>
	<script type="text/javascript" src="app/components/pagerange_panel/pagerange.controller.js"></script>
	<script type="text/javascript" src="app/components/preview/print.previewer.directive.js"></script>
	<script type="text/javascript" src="app/components/upload/upload.controller.js"></script>
	<script type="text/javascript" src="app/components/app/app.controller.js"></script>
	<script type="text/javascript" src="app/app.module.js"></script>
</html>