$(function(){
	if(localStorage){
		if(!localStorage.whoami){
			window.location = "/index.html";
		}
		
	}
	else{
		alert("Sorry. You cant use this app.")
	}

	console.log(JSON.parse(localStorage.whoami));
	var userObject = JSON.parse(localStorage.whoami);

	$("#logoutlink").click(function(){
		delete localStorage.whoami;
		window.location = "/index.html";

	})
	$("#token_container").html("Please use this token to authenticate :  "+ userObject.token)
	
	function getData(){
		$.ajax({
			url:host+"/currentweeksummary?token="+userObject.token,
			dataType:"JSON",
			success:function(response){
				console.log(response);
			}
		})
	}
	getData();
})