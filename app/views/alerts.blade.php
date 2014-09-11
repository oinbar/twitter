@extends('master')

@section('main-content')

<div>
    <ul class="feeds-items">			
		@foreach($data as $tweet)
			<li><a href="{{ url('/tweet/' . $tweet['_id']) }}"> {{ $tweet['text'] }} </a></li>
		@endforeach
		
	</ul>
</div>


@stop