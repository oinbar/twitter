@extends('master')

@section('main-content')

<div class="inner-container">
	<ul class="feeds-nav">
		<li><a href="/edit_feed">Add Feed</a></li>
		<li><a href="#">...</a></li>
		<li><a href="#">...</a></li>		
	</ul>
</div>

<div>
    <ul class="feeds-items">

    	@foreach($feeds as $feed)
    		<b>{{ $feed->feed_name }}</b>
			<li><a href="{{ url('/edit_feed/'.$feed->feed_id) }}"> edit </a></li>
			<li><a href="{{ url('/delete_feed/'.$feed->feed_id) }}"> delete </a></li>
			<li><a href="{{ url('/view_feed/'.$feed->feed_id) }}"> view </a></li>
		@endforeach
	</ul>
</div>


@stop