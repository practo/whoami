var host = "http://ec2-54-254-105-248.ap-southeast-1.compute.amazonaws.com";
$(function(){
	if(localStorage){
		if(!localStorage.whoami){
			window.location = "/index.html";
		}
		
	}
	else{
		alert("Sorry. You cant use this app.")
	}
	try{
		console.log(JSON.parse(localStorage.whoami));
	}
	catch(e){
		delete localStorage.whoami;
		window.location = "/index.html";
	}
			
	var userObject = JSON.parse(localStorage.whoami);

	$("#logoutlink").click(function(){
		delete localStorage.whoami;
		window.location = "/index.html";

	})

	$("#token_container").html("Please use this token to authenticate :  <strong>"+ userObject.token+"</strong>");
	$("#userdata").html(userObject.email);

	var number = Math.floor(Math.random()*75);
	$(".randomquote li").eq(number).show();
	
	function getData(){
		$.ajax({
			url:host+"/currentweeksummary?token="+userObject.token,
			dataType:"JSON",
			success:function(response){
				console.log(response);

				var jsonData = [];

				$.each(response.location_summary,function(key,dataPoint){
					jsonData.push({
						"value":Math.floor(dataPoint.duration_seconds/60),
						"label":dataPoint.name
					})
				})

				plotPie("chart1",jsonData);

			    
			}
		})

		
	}
	getData();

	FusionCharts.setCurrentRenderer('javascript');

	

	function plotPie(domId,data){

		chartObject = new FusionCharts({
            swfUrl: "Charts/Doughnut2D.swf",
            width: "100%",
            height: "350px"
    	});

		var chartData = {
	        "chart": {
	        	"numberPrefix":" mins",
	            "caption": "Where have you been ?",
	            "bgColor":"#ffffff",
	            "showvalues": "1",
	            "showlegend": "0",
	            "legendposition": "RIGHT",
	            slicingDistance: '25',
	            showBorder: "0",
	        },
	    	data:data};


	    chartObject.setJSONData(chartData);
	    chartObject.setTransparent(true);
	    chartObject.render(domId);
	}
})