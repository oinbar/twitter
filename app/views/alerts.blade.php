@extends('master')

@section('main-content')

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">           
	
    <ul class="feeds-items">			
		@foreach($data as $tweet)
			<li><a href="{{ url('/tweet/' . $tweet['_id']) }}"> {{ $tweet['text'] }} </a></li>
		@endforeach
		
	</ul>
</div>


@stop