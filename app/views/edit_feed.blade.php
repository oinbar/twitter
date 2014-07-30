@extends('master')

@section('main-content')

<h2>
	@if ($method=='post')
		Create new feed
	@else
		Edit feed: {{$name}}
	@endif
</h2>
	
{{ Form::open(array('url'=> '/edit_feed/' . $feed_id, 
					'method'=>$method, 
					'accept-charset' => 'ISO-8859-1')) }}
{{ Form::label('name', 'Feed Name: ') }} <br>					
{{ Form::text('name', $name) }} <br><br>

{{ Form::label('feed_criteria', 'Feed Criteria: ') }} <br>					
{{ Form::textarea('feed_criteria', $feed_criteria) }} <br><br>

{{ Form::label('update_rate', 'Update Rate: ')}}<br>
{{ Form::select('update_rate', array('hourly'=>'hourly', 
									 'daily'=>'daily', 
									 'weekly'=>'weekly'), $update_rate); }}<br><br>

{{form::submit('Submit')}}

{{ Form::close() }}

@stop
