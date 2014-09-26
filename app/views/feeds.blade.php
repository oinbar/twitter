@extends('master')

@section('main-content')

 <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">           
   <h1 class="page-header">My Feeds
   	<div class="pull-right"><a href="/edit_feed">Add+</a></div>
   </h1>
  
      <ol class="breadcrumb">
     <li><a href="#">My Feeds</a></li>
     <li class="active">blabla</li>
   </ol> 
   

    <ul class="well">
    	@foreach($feeds as $feed)
			<div class="well">
				<ul class="list-inline">
					<li>						
						<a href="{{ url('/view_feed/'.$feed->feed_id) }}"><h4>{{ $feed->feed_name }}</h4></a>						
					</li>				
					<li>					
						<a href="{{ url('/edit_feed/'.$feed->feed_id) }}">Edit</a>					
					</li>
					<li>					
						<a href="{{ url('/delete_feed/'.$feed->feed_id) }}">Delete</a>
					</li>

				</ul>
				<label>Number of tweets: </label><br>
				<label>Tweets perhour: </label><br>
				<label>Credentials: </label><br>
				<a href="/analytics"><h4>Analytics</h4></a>
				<a href="/alerts/{{ $feed->feed_id }}"><h4>Alerts</h4></a>
			</div>			
		@endforeach
	</ul>

	 
</div>

@stop