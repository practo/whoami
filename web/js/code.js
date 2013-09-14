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

	$("#logoutlink").click(function(){
		delete localStorage.whoami;
		window.location = "/index.html";

	})

	
})