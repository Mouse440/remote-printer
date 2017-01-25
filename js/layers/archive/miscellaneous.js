"USE STRICT"
var Miscellaneous = (function(fPage) {

	function _displayApolNote() {
		fPage.toggleAlert("We apologize for the inconvience","<h4>Luckily! You can" +
			 " revert to the previously working version <br> via the link on the main page.</h4>","black",true);
	}

	/*
	* Bind permission button to store file
	*/
	function bindPermissionBtns() {
		$("#noPermissionBtn").on('click', function(e) { 
			var data = {
				'action':'store',
			'permission':'false', 			//false since noPermissionBtn was clicked
			 'fileName' : $(this).prop('fileName')  
			};

			$.ajax({ 						//send permission to server
				data: data,
				url: 'php/phase2/processor.php',
				type: 'POST',
			}).done(function(){
				_displayApolNote();
			})

		});

		$("#permissionBtn").on('click', function(){
			_displayApolNote();
		});
	}

	/*
	* bind feed back modal events
	*/
	function bindFBModalEvents() {

		//create dynamic suggestion message
		$('#feedbackModal').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget); 									// Button that triggered the modal
		  var suggestion = button.data('suggestion'); 							// Extract info from data-* attributes
		  var modal = $(this);
		  modal.find('#feedback-suggestion-label').text(suggestion);
		  //modal.find('#message-text').val('');//wipe any existing text4

		}).on("hide.bs.modal",function(){ 										//hide error message
			$("#feedback-error-msg").css("display","none");
		});
	}

	/*
	* bind feed back submit buttons
	*/
	function bindFBSubmitBtn() {
		//submit button listeners
		$("#feedback-submit-btn").on("click", function(){ //submit to server
			var data = {
				  'action':'feedback',
				'question':$('#feedback-suggestion-label').text(),
				  'answer':$("#message-text").val()
			};

			if(data.answer !== '') { 											//text field is not empty
					$.ajax({
					type: 'POST',
					data: data,
					url: 'php/phase2/processor.php',
					success: function(data) {
						console.log(data);
					},
				}).done(function(){
					$("#feedbackModal").modal('hide');
					fPage.toggleAlert("=)","<h4>Thank you for your feedback!</h4>","green",true);
				});
			} else { 															//show error message
				$("#feedback-error-msg").css("display","block");
			}
		});
	}

	//public methods
	return {
		init: function() {
			bindPermissionBtns();
			bindFBModalEvents();
			bindFBSubmitBtn();
		},
		unlinkFile: function() {
			$.ajax({
				url: 'php/sub_phases/cancelTransaction.php',
				type: 'POST',
				success: function(e){
					console.log('success! ' + e);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
					//setUploaded(false);
			        console.log("Error: " + errorThrown);
			    }

			})
		}
	}
})(FrontPage);
