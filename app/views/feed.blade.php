@extends('master')

@section('main-content')


<h1 class="sub-header">Feed: <span style="opacity: 0.7"> {{ $feed->feed_name }}</span></h1>



@if ($feed->feed_status == 'on')
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
		<li class="enabled"><a href="{{ url('/view_feed/'.$feed->feed_id.'/' . 1) }}">&laquo;</a></li>
	@else
		<li class="disabled"><a href="{{ url('/view_feed/'.$feed->feed_id.'/' . 1) }}">&laquo;</a></li>
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
    		<li class="active"><a href="{{ url('/view_feed/'.$feed->feed_id.'/' . $i) }}"> {{ $i }}<span class="sr-only">(current)</span></a></li>
    	@else
    		<li><a href="{{ url('/view_feed/'.$feed->feed_id.'/' . $i) }}">{{ $i }}</a></li>
    	@endif
    @endfor
    @if($page_num == $total_pages-1)
    	<li class="disabled"><a href="{{ url('/view_feed/'.$feed->feed_id.'/' . ($total_pages-1)) }}">&raquo;</a></li>
    @else
    	<li class="enabled"><a href="{{ url('/view_feed/'.$feed->feed_id.'/' . ($total_pages-1)) }}">&raquo;</a></li>
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

	

@stop



<!-- @section('secondary-navbar')

<li><a href="/feeds"> My Feeds </a></li>

@foreach($feeds as $loopfeed)
	@if ( $loopfeed->feed_id == $feed->feed_id )
		<li class="active"><a href="{{ url('/view_feed/' . $loopfeed->feed_id) }}"> -> {{ $loopfeed->feed_name }} </a></li>
	@else
		<li><a href="{{ url('/view_feed/' . $loopfeed->feed_id) }}"> -> {{ $loopfeed->feed_name }} </a></li>
	@endif
@endforeach

 -->

