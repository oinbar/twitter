@extends('master')

@section('main-content')

<nav class="nav nav-pills inner-header" role="navigation">     
	<ul class="nav nav-pills pull-left">
		<li><a href="/edit_feed">Add</a></li>
		<li><a href="#">...</a></li>
		<li><a href="#">...</a></li>		
	</ul>
</nav>

<div>
    <ul class="well">

    	@foreach($feeds as $feed)
			<div class="well">
				<ul class="list-inline">
					<li>
						<div class="button1">				
							<a href="{{ url('/view_feed/'.$feed->feed_id) }}"><h4>{{ $feed->feed_name }}</h4></a>
						</div>
					</li>				
					<li>
						<div class="button1">
							<button class="btn btn-sm btn-block glyphicon glyphicon-edit" href="{{ url('/edit_feed/'.$feed->feed_id) }}">Edit </button>
						</div>
					</li>
					<li>
						<div class="button1">
							<button class="btn btn-sm btn-block btn-danger glyphicon glyphicon-trash" href="{{ url('/delete_feed/'.$feed->feed_id) }}">Delete </button>
						</div>
					</li>

				</ul>
				<label>Number of tweets: </label><br>
				<label>Tweets perhour: </label><br>
				<label>Credentials: </label><br>
			</div>			
		@endforeach
	</ul>
</div>


@stop