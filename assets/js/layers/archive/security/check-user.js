/*
	!!!!!! DEPRECATED !!!!!!
*/
$.ajax({
	url: "php/security/checkLogin.php",
	async: false,
	success: function(data) {
		if(data === 'false') {
			window.location = "../../";
		}
	},
	/*error: function(XMLHttpRequest, textStatus, errorThrown) { 
						//setUploaded(false);
						$("#ajax-loader1").css("display","none");  
				        alert("Error: " + errorThrown + ".\nPlease contact an officer for help.");
				    }*/
});
