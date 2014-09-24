
@extends('master')

@section('main-content')


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">           

	<h2>Mongo Query:</h2>
	{{ Form::open(array('url'=> '/mongoquery/', 
						'method' => 'GET',
						'accept-charset' => 'ISO-8859-1')) }}
	{{ Form::label('query', 'type full query: ') }} <br>					

	@if ($query)
		{{ Form::textarea('query', $query) }} <br><br>
	@else
		{{ Form::textarea('query', 'db.data1.find().toArray()') }} <br><br>
	@endif

	{{form::submit('Submit')}}

	{{ Form::close() }}

	<?php
		
	echo Pre::render($results);
	?>


</div>

@stop