@extends('master')

@section('main-content')

           
   <h1 class="page-header">My Feeds</h1>
      <ol class="breadcrumb">
     <li><a href="#">My Feeds</a></li>
     <li class="active">blabla</li>
   </ol> 
   

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
				<a href="#"><h4>Analytics</h4></a>
				<a href="#"><h4>Alerts</h4></a>
			</div>			
		@endforeach
	</ul>

 
 </div>
</div>

@stop