

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <div class="collapsableContainer">
    <div class="collapsableHeaderDataOverview"><span><h2 class="sub-header">- Data Overview</h2></span></div>
    

    <div class="collapsableContent">

    {{-- TOGGLE BUTTON --}}
      {{ HTML::script('/ui1/jquery.min.js'); }}
      {{ HTML::style('/ui1/bootstrap-switch.css'); }}
      {{ HTML::script('/ui1/bootstrap-switch.min.js'); }}

      <div id="status-toggle-container">
        @if ($feed->feed_status == 1)
            <input id="status-toggle" type="checkbox" name="feed-status-checkbox" checked>
        @else
            <input id="status-toggle" type="checkbox" name="feed-status-checkbox" unchecked>
        @endif
      </div>

      <script>
      $("[name='feed-status-checkbox']").bootstrapSwitch();
      </script>

      <script>
        $(document).ready(function() {
            // TODO - Reset toggle if the feedstatus change did not work
            $(".bootstrap-switch-container").click(function() {
                var feed_status = null;
                var feed_id = "<?php echo $feed->feed_id; ?>";
                $.get("/getFeedStatus/" + feed_id, function(data){
                    feed_status = data;
                    if (feed_status == '0') {
                        $.get("/startfetch/" + feed_id)
                    }
                    else {
                        $.get("/stopfetch/" + feed_id)
                    }
                });
            });
        });
      </script>


        {{-- model view params --}}
        {{ Form::open(array('url'=> '/modelparams/' . $feed->id,
      						'method' => 'POST',
      						'accept-charset' => 'ISO-8859-1')) }}

        <h4>Columns:</h4>
        {{ Form::text('columns', 'col1, col2, col3') }} <br>

        <h4>Filters:</h4>
        {{ Form::text('filter', 'field : {$gt : value...}, additional filter...') }} <br><br>

        {{ form::submit('Apply')}}
        {{ Form::close() }} <br><br>

        {{ Form::label('search', 'Search: ') }} <br>
        {{ Form::text('name', '') }} <br><br>


        {{-- Table --}}
        <div id="dataTable" class="table-responsive">
         <table class="table table-striped">
           <thead>
             <tr>
               {{--<th>Tweets ({{ ($page_num-1) * $take }} - {{ ($page_num-1) * $take + $take }} / {{ $total_records }} ):</th>--}}
               <th>Tweets </th>
               <th>Date</th>
               <th># of Retweets</th>
             </tr>
           </thead>
           <tbody>

            {{--@foreach($tweets as $tweet)--}}
             {{--<tr>--}}
               {{--<td><a href="{{ url('/tweet/' . $tweet['_id']) }}" >{{ $tweet['text'] }}</a></td>--}}
               {{--<td>{{ $tweet['datetime'] }}</td>--}}
               {{--<td>{{ $tweet['retweet_count'] }}</td>--}}
             {{--</tr>--}}
            {{--@endforeach--}}

           </tbody>
         </table>
        </div>

        {{-- Pagination --}}
        <center>
          <ul id="pagination" class="pagination"></ul>
        </center>

        <script>
            $(document).ready(function() {
                var feed_id = "<?php echo $feed->feed_id; ?>";
                $.getJSON("/get_feed_data_json/" + feed_id + "/" + "1", function(data){
                    $.each(data.data, function(index){
                        newRow = "<tr>" +
                                        "<td><a href=/tweet/" + data.data[index]._id + ">" + data.data[index].text + "</td>" +
                                        "<td>" + data.data[index].datetime + "</td>" +
                                        "<td>" + data.data[index].retweet_count + "</td>" +
                                     "</tr>"
                        $("#dataTable tr:last").after(newRow);
                    });
                });
            });
        </script>


      {{-- COLLAPSE --}}
      <script src="/ui1/jquery.min.js"></script>
      <script>

      $(".collapsableHeaderDataOverview").click(function () {

      $header = $(this);
      //getting the next element
      $content = $header.next();
      //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
      $content.slideToggle(500, function () {
          //execute this after slideToggle is done
          //change text of header based on visibility of content div
          $header.text(function () {
              //change text based on condition
              return $content.is(":visible") ? "- Data Overview" : "+ Data Overview";
              }).wrapInner('<span><h2 class="sub-header"></h2></span>');
          });
      });
      </script>


      {{-- PAGINATION --}}
      <script src="/ui1/jquery.twbsPagination.min.js"></script>
      <script>

        var total_pages = "<?php echo $total_records ?>";
        $('#pagination').twbsPagination({
            totalPages: total_pages / 5,
            visiblePages: 7,
            onPageClick: function (event, page) {
                var feed_id = "<?php echo $feed->feed_id; ?>";
                $.getJSON("/get_feed_data_json/" + feed_id + "/" + page, function(data){
                    $("#dataTable tr").empty();
                    $.each(data.data, function(index){
                        newRow = "<tr>" +
                                        "<td><a href=/tweet/" + data.data[index]._id + ">" + data.data[index].text + "</td>" +
                                        "<td>" + data.data[index].datetime + "</td>" +
                                        "<td>" + data.data[index].retweet_count + "</td>" +
                                     "</tr>"
                        $("#dataTable tr:last").after(newRow);
                    });
                });

            }
        });

      </script>
    </div>
  </div>
</div>
