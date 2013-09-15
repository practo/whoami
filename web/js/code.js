var host = "http://ec2-54-254-105-248.ap-southeast-1.compute.amazonaws.com";

var activityGroupJsonData = [];
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

				var locationJsonData = [];
				var activityJsonData = [];
				var activityHomeJsonData = [];
				var activityWorkJsonData = [];

				$.each(response.location_summary,function(key,dataPoint){
					locationJsonData.push({
						"value":Math.floor(dataPoint.duration_seconds/60),
						"label":dataPoint.name
					})
				})

				$.each(response.activity_summary,function(key,dataPoint){
					activityJsonData.push({
						"value":Math.floor(dataPoint.duration_seconds/60),
						"label":dataPoint.name
					})
				})

				$.each(response.activity_home_summary,function(key,dataPoint){
					activityHomeJsonData.push({
						"value":Math.floor(dataPoint.duration_seconds/60),
						"label":dataPoint.name
					})
				})

				$.each(response.activity_work_summary,function(key,dataPoint){
					activityWorkJsonData.push({
						"value":Math.floor(dataPoint.duration_seconds/60),
						"label":dataPoint.name
					})
				})

				plotPie("chart_location",locationJsonData);

				plotPie("chart_activity_summary",activityJsonData);
				plotPie("chart_activity_home_summary",activityHomeJsonData);
				plotPie("chart_activity_work_summary",activityWorkJsonData);

			    
			}
		})

		$.ajax({
			url:host+"/activity/group?token="+userObject.token,
			dataType:"JSON",
			success:function(response){
				console.log(response);

				var knownNames = [{"1":'facebook'},{"2":'mail'},{"3":'youtube'},{"4":'evernote'},{"5":'github'},{"6":'phone'},{"7":'pirate'}]

				

				$.each(response,function(key,dataPoint){
					var isKnown = false;
					$.each(knownNames,function(index,name){
						if(dataPoint.activity.indexOf(name) != -1){
							isKnown = true;
							var found = false;

							$.each(activityGroupJsonData,function(j,eachActivity){
								if(eachActivity.label == name){
									found = true;
									eachActivity.value = parseInt(eachActivity.value,10) + dataPoint.total_time;
									return false;
								}
								
							})
							if(found){
								
								return false;
							}
							else{
								activityGroupJsonData.push({
									"value":Math.floor(dataPoint.duration_seconds/60),
									"label":dataPoint.name
								})
							}
						}
						else{

						}
					})

					if(!isKnown){
						activityGroupJsonData.push({
							"value":Math.floor(dataPoint.duration_seconds/60),
							"label":dataPoint.name
						})
					}

					
				})

				plotPie("group_summary",activityGroupJsonData);

				

			    
			}
		})


	}
	getData();

	FusionCharts.setCurrentRenderer('javascript');

	

	function plotPie(domId,data){

		chartObject = new FusionCharts({
            swfUrl: "Charts/Doughnut2D.swf",
            width: "100%",
            height: "300px"
    	});

		var chartData = {
	        "chart": {
	        	"numberPrefix":" mins",
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