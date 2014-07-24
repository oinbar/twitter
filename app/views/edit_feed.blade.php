@extends('master')

@section('main-content')
	
{{ Form::open(array('url'=> '/edit_feed', 
					'method'=>'POST', 
					'accept-charset' => 'ISO-8859-1')) }}
{{ Form::label('feed_name', 'Feed Name: ') }} <br>					
{{ Form::text('feed_name') }} <br><br>

{{ Form::label('feed_criteria', 'Feed Criteria: ') }} <br>					
{{ Form::textarea('feed_criteria') }} <br><br>

{{ Form::label('update_rate', 'Update Rate: ')}}<br>
{{ Form::select('update_rate', array('hourly', 'daily', 'weekly')); }}<br><br>

{{ Form::close() }}

@stop
