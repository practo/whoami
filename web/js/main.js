$(function(){

	//var host = "http://ec2-54-254-105-248.ap-southeast-1.compute.amazonaws.com";
	var host = '';

	if(localStorage){
		
	
		if(localStorage.whoami){
			window.location = "/data.html";
		}
		
	}
	else{
		alert("Sorry. You cant use this app.");
	}

	$("#signinform button").click(function(e){
		e.preventDefault();

		$.ajax({
			url: host + "/users/authenticates",
			type:"POST",
			data:$("#signinform").serialize(),
			dataType:"JSON",
			success:function(response){

				
				localStorage.whoami = JSON.stringify(response);
			
				window.location = "/data.html";
				

				
			},
			error:function(){
				alert("Sorry. Could not log you in.")
			}
		})

	})
	$("#signupform button").click(function(e){
		e.preventDefault();

		$.ajax({
			url: host + "/users",
			type:"POST",
			data:$("#signupform").serialize(),
			dataType:"JSON",
			success:function(response){
				localStorage.whoami = JSON.stringify(response);
				
				window.location = "/data.html";
				
			},
			error:function(){
				alert("Sorry. Could not log you in.")
			}
		})

	})	

	if($("#chart1").length){
		renderSampleCharts();
	}

	function renderSampleCharts(){
		FusionCharts.setCurrentRenderer('javascript');

		chartObject1 = new FusionCharts({
	            swfUrl: "Charts/Doughnut2D.swf",
	            width: "100%",
	            height: "350px"
	    });
	    chartObject2 = new FusionCharts({
	            swfUrl: "Charts/Doughnut2D.swf",
	            width: "100%",
	            height: "350px"
	    });
	    chartObject3 = new FusionCharts({
	            swfUrl: "Charts/Doughnut2D.swf",
	            width: "100%",
	            height: "350px"
	    });

	    chartObject4 = new FusionCharts({
	            swfUrl: "Charts/Doughnut2D.swf",
	            width: "100%",
	            height: "350px"
	    });

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
	        data:[{label: "Work",
	                  value: 10},
	                { label: "Home",
	                  value: 20},
	                 {label: "Travel",
	                  value: 5},
	                  {label: "Other",
	                  value: 15}
	                  ]
	    };
	    var chartData2 = {
	        "chart": {
	            "caption": "What do I do at home ?",
	            "bgColor":"#ffffff",
	            "showvalues": "1",
	            "showlegend": "0",
	            "legendposition": "RIGHT",
	            slicingDistance: '25',
	            showBorder: "0",
	        },
	        data:[{label: "Facebook",
	                  value: 15},
	                { label: "Youtube",
	                  value: 20},
	                   { label: "Gaming",
	                  value: 20},
	                   { label: "Chat",
	                  value: 20},
	                   { label: "Productivity",
	                  value: 20},
	                 {label: "Sleep",
	                  value: 200},
	                  {label: "Not tracked",
	                  value: 10}
	                  ]
	    };
	    var chartData3 = {
	        "chart": {
	            "caption": "What do I do at work ?",
	            "bgColor":"#ffffff",
	            "showvalues": "1",
	            "showlegend": "0",
	            "legendposition": "RIGHT",
	            slicingDistance: '25',
	            showBorder: "0",
	        },
	        data:[{label: "Excel",
	                  value:60},
	                { label: "Powerpoint",
	                  value: 90},
	                 {label: "Mail",
	                  value: 50},
	                  {label: "Other",
	                  value: 15}
	                  ]
	    };

	    var chartData4 = {
	        "chart": {
	            "caption": "",
	            "bgColor":"#ffffff",
	            "bgAlpha":"0,0",
	            "showvalues": "1",
	            "showlegend": "0",
	            "legendposition": "RIGHT",
	            slicingDistance: '25',
	            showBorder: "0",
	        },
	        data:[{label: "Productivity",
	                  value:90},
	                { label: "Entertainment",
	                  value: 90},
	                 {label: "Sleep",
	                  value: 50},
	                  {label: "Travel",
	                  value: 25},{
	              	  label: "Networking",
	                  value: 15}
	                  ]
	    };


	    chartObject1.setJSONData(chartData1);
	    chartObject2.setJSONData(chartData2);
	    chartObject3.setJSONData(chartData3);
	    chartObject4.setTransparent(true);
	    chartObject4.setJSONData(chartData4);

	    chartObject1.render("chart1");
	    chartObject2.render("chart2");
	    chartObject3.render("chart3");
	    chartObject4.render("chart4");
	}

	

})