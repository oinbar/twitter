

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <div class="collapsableContainer">
    <div class="collapsableHeaderTrends"><span><h2 class="sub-header">- Trends</h2></span></div>


        <div class="collapsableContent">
        <div id="graphdiv"></div>

        <script type="text/javascript" src="/ui1/dygraph-combined.js"></script>
        <script type="text/javascript" src="/ui1/jquery.min.js"></script>
        <div id="graphdiv"></div>
        <script type="text/javascript">

        $(document).ready(function() {
            var feed_id = "<?php echo $feed->feed_id; ?>";
            $.getJSON("/get_trends_data/" + feed_id, function(data){
                console.log(data);
                var labels = [];
                for (element in data.labels){
                    labels.push(data.labels[element]);
                }
                var graphData = []
                for (element in data.data){
                    data.data[element][0] =new Date(Date.parse(data.data[element][0]));
                    graphData.push(data.data[element]);
                }
                g = new Dygraph(
                    document.getElementById("graphdiv"),
                    graphData,
                    {labels : labels});
            });
        });

        </script>

        </div>
      <script src="/ui1/jquery.min.js"></script>
      <script>

      $(".collapsableHeaderTrends").click(function () {

      $header = $(this);
      //getting the next element
      $content = $header.next();
      //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
      $content.slideToggle(500, function () {
          //execute this after slideToggle is done
          //change text of header based on visibility of content div
          $header.text(function () {
              //change text based on condition
              return $content.is(":visible") ? "- Trends" : "+ Trends";
              }).wrapInner('<span><h2 class="sub-header"></h2></span>');
          });
      });
      </script>

  </div>
</div>
