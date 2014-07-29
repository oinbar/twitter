@extends('master')

@section('main-content')

<div class="inner-container">
	<center> Feed: {{ $feed_id }} </center>
	<center> Results: {{ $start }} - {{ $end }} </center>
	<center> {{ $num_records }} records </center>
	<center> <a href="{{ url('/fetch/' . $feed_id) }}" >fetch</a> </center><br><br>
</div>

<div>
    <ul class="feeds-items">
		
		
		@foreach($statuses as $status)
			<li><a href="#"> {{ $status }} </a></li>
		@endforeach
		
	</ul>
</div>


@stop