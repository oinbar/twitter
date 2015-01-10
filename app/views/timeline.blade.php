
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 <html>

    
    <body onload="onLoad();" onresize="onResize();">
    	
    	<!-- <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		  <div class="collapsableContainer">
		    <div class="collapsableHeader"><span><h2 class="sub-header">+ Data Overview</h2></span></div>
		 -->    

    	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		    <div class="collapsableContainer">
			    <div class="collapsableHeader"><span><h2 class="page-header">- Alerts</h2></span></div>
			    	<div class="collapsableContent">
		   				<p class="lead"><i>Here are your upcomming events:</i></p>		   
		    			<div id="my-timeline" style="height: 150px; border: 1px solid #aaa"></div>
		    		</div>
		    	</div>
		    </div>
		</div>

    </body>


		
    <script>

    	$(".collapsableHeader").click(function () {
			$header = $(this);
			//getting the next element
			$content = $header.next();
			//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
			$content.slideToggle(500, function () {
			  //execute this after slideToggle is done
			  //change text of header based on visibility of content div
			  $header.text(function () {
			      //change text based on condition
			      return $content.is(":visible") ? "- Alerts" : "+ Alerts";
			      }).wrapInner('<span><h2 class="sub-header"></h2></span>');
			});

		});
	</script>



    <script>
		var tl;
		function onLoad() {

		    SimileAjax.History.enabled = false;

			var eventSource = new Timeline.DefaultEventSource();			
			
			var evt = new Timeline.DefaultEventSource.Event ({
			    start: new Date("Thu, 11 Sep 2014 04:25:44 +0000"),
			    instant : true,
			    text : "An event",
			    description : "A description"
			});

			eventSource.add(evt);



			var feed_id = "<?php echo $feed->feed_id; ?>";
            $.getJSON("/get_alerts_data/" + feed_id, function(data){
                $.each(data, function(index){
                    var evt = new Timeline.DefaultEventSource.Event ({
                        start: new Date(data[index].full_datetime),
                        instant: true,
                        text: data[index].future_time_norm + ", " + data[index].location,
                        description: data[index].text
                    });

                    eventSource.add(evt);
                });

                var bandInfos = [
                Timeline.createBandInfo({
                    eventSource:    eventSource,
                    date:           new Date(),
                    width:          "70%",
                    intervalUnit:   Timeline.DateTime.DAY,
                    intervalPixels: 100
                }),
                Timeline.createBandInfo({
                    overview:       true,
                    eventSource:    eventSource,
                    date:           new Date(),
                    width:          "30%",
                    intervalUnit:   Timeline.DateTime.MONTH,
                    intervalPixels: 200
                })
                ];
                bandInfos[1].syncWith = 0;
                bandInfos[1].highlight = true;

                tl = Timeline.create(document.getElementById("my-timeline"), bandInfos);
                tl.layout();
            });
	 	}

		var resizeTimerID = null;
		function onResize() {
		    if (resizeTimerID == null) {
		        resizeTimerID = window.setTimeout(function() {
		            resizeTimerID = null;
		        }, 500);
		    }
		}
	</script>

 </html>

 
