<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 <html>
	<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />	
	
 	<script>
    	Timeline_ajax_url="http://api.simile-widgets.org/ajax/2.2.1/simile-ajax-api.js";
        Timeline_urlPrefix='http://api.simile-widgets.org/timeline/2.3.1/';       
        Timeline_parameters='bundle=true';
 	</script>

	<script src="http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js" type="text/javascript"></script>
	 	

    </head>
    
    <body onload="onLoad();" onresize="onResize();">
     
	    <div id="my-timeline" style="height: 150px; border: 1px solid #aaa"></div>
		<noscript>
			This page uses Javascript to show you a Timeline. Please enable Javascript in your browser to see the full page. Thank you.
		</noscript>

    </body>


    	<script>	
		var tl;
		function onLoad() {	

			var eventSource = new Timeline.DefaultEventSource();			
			
			var evt = new Timeline.DefaultEventSource.Event ({
			    start: new Date("Thu, 11 Sep 2014 04:25:44 +0000"),
			    instant : true,
			    text : "An event",
			    description : "A description"
			});

			eventSource.add(evt);

			var data = {{ $data }}	
			for (var key in data) {
				if (data.hasOwnProperty(key)) {
					var evt = new Timeline.DefaultEventSource.Event ({			    	
			    		start: new Date(data[key]["full_datetime"]),
			    		instant: true,
			    		text: data[key]["future_time_norm"] + ", " + data[key]["location"],
			    		description: data[key]["text"]
			    	});

			    	eventSource.add(evt);
			  	}
			}

			

			var bandInfos = [
			Timeline.createBandInfo({
			    eventSource:    eventSource,
			    date:           new Date(),
			    width:          "70%", 
			    intervalUnit:   Timeline.DateTime.HOUR, 
			    intervalPixels: 100
			}),
			Timeline.createBandInfo({
			    overview:       true,
			    eventSource:    eventSource,
			    date:           new Date(),
			    width:          "30%", 
			    intervalUnit:   Timeline.DateTime.DAY, 
			    intervalPixels: 200
			})
			];
		    bandInfos[1].syncWith = 0;
		    bandInfos[1].highlight = true;
		   
		    tl = Timeline.create(document.getElementById("my-timeline"), bandInfos);

		    // tl.loadJSON("timelinedata.json", function(json, url) { eventSource.loadJSON(json, url); });
		   
	 	}

		var resizeTimerID = null;
		function onResize() {
		    if (resizeTimerID == null) {
		        resizeTimerID = window.setTimeout(function() {
		            resizeTimerID = null;
		            tl.layout();
		        }, 500);
		    }
		}
	</script>
    
 </html>