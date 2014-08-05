@extends('master')

@section('main-content')

<div class="inner-container">
	<center> Feed: {{ $feed->feed_name }} </center>
	<center> {{ $num_records }} records </center><br>

	<center>
		<a href="{{ url('/view_feed/'.$feed->feed_id.'/'.$prev) }}"> << prev </a> 
		Results: {{ $start }} - {{ $end }} 
		<a href="{{ url('/view_feed/'.$feed->feed_id.'/'.$end) }}">next >> </a> 
	</center>

	@if ($feed->feed_status == 'on')
		<center> <a href="{{ url('/stopfetch/' . $feed_id) }}" >Stop Fetching</a> </center><br><br>
	@else
		<center> <a href="{{ url('/startfetch/' . $feed_id) }}" >Start Fetching</a> </center><br><br>
	@endif

</div>

<div>
    <ul class="feeds-items">
		
		
		@foreach($statuses as $tweet)
			<li><a href="#"> {{ $tweet }} </a></li>
		@endforeach
		
	</ul>
</div>


@stop