$(function(){
	if(localStorage){
		if(!localStorage.whoami){
			window.location = "/index.html";
		}
		else{
			if(!localStorage.whoami.user.token){
				window.location = "/index.html";
			}
		}
	}
	else{
		alert("Sorry. You cant use this app.")
	}

	console.log(JSON.parse(localStorage.whoami));



	
})