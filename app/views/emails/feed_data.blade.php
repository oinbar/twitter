{{ HTML::style('/css/css.css'); }}
{{ HTML::style('/bootstrap/css/bootstrap.css'); }}

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Up2Data</title>
      {{-- TODO: add <link rel="icon" href="../../favicon.ico"> tags --}}

      {{ HTML::style('/ui1/bootstrap.min.css'); }}
      {{-- Custom css for this site --}}
      {{ HTML::style('/ui1/ui1_inside.css'); }}
      {{-- IE10 viewport hack for surface/desktop Windows 8 bug --}}
      {{ HTML::script('/ui1/ie10-viewport-bug-workaround.js'); }}
      {{-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries --}}
      <!--[if lt IE 9]>
        {{ HTML::script('/ui1/html5shiv_v3.7.2.min.js'); }}
        {{ HTML::script('/ui1/respond_v1.4.2.min.js'); }}
      <![endif]-->
   </head>

   <body>
    
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <h1 class="sub-header">Feed: <span style="opacity: 0.7"> {{ $feed->feed_name }}</span></h1>



  @if ($feed->feed_status == 1)
    <center>Status: ON</center>
    <center> <a href="{{ url('/stopfetch/' . $feed->feed_id) }}" >Stop Fetching</a> </center>
  @else
    <center>Status: OFF</center>
    <center> <a href="{{ url('/startfetch/' . $feed->feed_id) }}" >Start Fetching</a> </center>
  @endif



    {{-- Pagination --}}
    <center>
      <ul class="pagination">
    @if($page_num > 1)
      <li class="enabled"><a href="{{ url('/view_feed_data/'.$feed->feed_id.'/' . 1) }}">&laquo;</a></li>
    @else
      <li class="disabled"><a href="{{ url('/view_feed_data/'.$feed->feed_id.'/' . 1) }}">&laquo;</a></li>
    @endif  
    <?php 
    $total_pages = $total_records/$take;
    $remainder = 0;
    if ($page_num < 5) {
      $remainder = 6 - $page_num; 
    } elseif ($page_num > $total_pages - 5) {
      $remainder = $total_records/$take - $page_num - 6;
    }
    ?>
      @for($i = $page_num - 5 + $remainder; $i <= $page_num + 5 + $remainder; $i++)     
        @if($i == $page_num)
          <li class="active"><a href="{{ url('/view_feed_data/'.$feed->feed_id.'/' . $i) }}"> {{ $i }}<span class="sr-only">(current)</span></a></li>
        @else
          <li><a href="{{ url('/view_feed_data/'.$feed->feed_id.'/' . $i) }}">{{ $i }}</a></li>
        @endif
      @endfor
      @if($page_num == $total_pages-1)
        <li class="disabled"><a href="{{ url('/view_feed_data/'.$feed->feed_id.'/' . ($total_pages-1)) }}">&raquo;</a></li>
      @else
        <li class="enabled"><a href="{{ url('/view_feed_data/'.$feed->feed_id.'/' . ($total_pages-1)) }}">&raquo;</a></li>
      @endif
     </ul>
  </center>

  {{-- Table --}}
  <div class="table-responsive">
   <table class="table table-striped">
     <thead>
       <tr>
         <th>Tweets ({{ ($page_num-1) * $take }} - {{ ($page_num-1) * $take + $take }} / {{ $total_records }} ):</th>
         <th>Date</th>
         <th>Retweets</th>
       </tr>
     </thead>
     <tbody>

      @foreach($data as $tweet)
       <tr>
         <td><a href="{{ url('/tweet/' . $tweet['_id']) }}" >{{ $tweet['text'] }}</a></td>
         <td>{{ $tweet['datetime'] }}</td>
         <td>{{ $tweet['retweet_count'] }}</td>
       </tr>
       @endforeach

     </tbody>
   </table>

  </div>
</div>





