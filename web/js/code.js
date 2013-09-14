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

				var jsonData = [];

				$.each(response.location_summary,function(key,dataPoint){
					jsonData.push({
						"value":dataPoint.duration_seconds,
						"label":dataPoint.name
					})
				})

				

			    var chartData1 = {
			        "chart": {
			            "caption": "Where have you been ?",
			            "bgColor":"#ffffff",
			            "showvalues": "1",
			            "showlegend": "0",
			            "legendposition": "RIGHT",
			            slicingDistance: '25',
			            showBorder: "0",
			        },
		        	data:jsonData};


		        chartObject1.setJSONData(chartData1);
		        chartObject1.setTransparent(true);
		        chartObject1.render("chart1");
			}
		})
	}
	getData();

	FusionCharts.setCurrentRenderer('javascript');

		chartObject1 = new FusionCharts({
	            swfUrl: "Charts/Doughnut2D.swf",
	            width: "100%",
	            height: "350px"
	    });
})