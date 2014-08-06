@extends('master')

@section('main-content')

<div class="inner-container">
	<center> Feed: {{ $feed->feed_name }} </center>
	<center> {{ $num_records }} records </center><br>

	@if ($feed->feed_status == 'on')
		Status: ON
		<center> <a href="{{ url('/stopfetch/' . $feed_id) }}" >Stop Fetching</a> </center><br><br>
	@else
		Status: OFF
		<center> <a href="{{ url('/startfetch/' . $feed_id) }}" >Start Fetching</a> </center><br><br>
	@endif

	<center>
		<a href="{{ url('/view_feed/'.$feed->feed_id.'/'.$prev) }}"> << prev </a> 
		Results: {{ $start }} - {{ $end }} 
		<a href="{{ url('/view_feed/'.$feed->feed_id.'/'.$end) }}">next >> </a> <br>
	</center>

</div>

<div>
    <ul class="feeds-items">
		
		
		@foreach($statuses as $tweet)
			<li><a href="#"> {{ $tweet }} </a></li>
		@endforeach
		
	</ul>
</div>


@stop