@extends('master')

@section('main-content')

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">           
	<h2>
		@if ($new_feed)
			Create new feed
		@else
			Edit feed: {{$name}}
		@endif
	</h2>
		
	{{ Form::open(array('url'=> '/edit_feed/' . $feed_id, 
						'method' => 'POST',
						'accept-charset' => 'ISO-8859-1')) }}
	{{ Form::label('name', 'Feed Name: ') }} <br>					
	{{ Form::text('name', $name) }} <br><br>

	{{ Form::label('criteria', 'criteria: ') }} <br>					
	{{ Form::textarea('criteria', $criteria) }} <br><br>

	{{ Form::label('update_rate', 'Update Rate: ')}}<br>
	{{ Form::select('update_rate', array('hourly'=>'hourly', 
										 'daily'=>'daily', 
										 'weekly'=>'weekly'), $update_rate); }}<br><br>

	{{ Form::label('status', 'Status: ')}}<br>									 
	{{ Form::select('status', array('off'=>'off', 
									'on'=>'on'), $status); }}<br><br>

	{{form::submit('Submit')}}

	{{ Form::close() }}

</div>

@stop
