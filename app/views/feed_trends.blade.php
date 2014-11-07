

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <div class="collapsableContainer">
    <div class="collapsableHeaderTrends"><span><h2 class="sub-header">- Trends</h2></span></div>


        <div class="collapsableContent">

        <script type="text/javascript" src="/ui1/dygraph-combined.js"></script>
        <div id="graphdiv"></div>
        <script type="text/javascript">
          g = new Dygraph(

            // containing div
            document.getElementById("graphdiv"),

            // CSV or path to a CSV file.
            "Date,Temperature\n" +
            "2008-05-07,75\n" +
            "2008-05-08,70\n" +
            "2008-05-09,80\n"

          );
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
