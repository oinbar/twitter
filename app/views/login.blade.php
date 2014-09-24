@extends('master')

@section('main-content')

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">           
	@if (Session::get('flash_message'))
		{{ Session::get('flash_message') }}
	@endif

	@foreach($errors->all() as $message) 
		<div class='error'>{{ $message }}</div>
	@endforeach 


	<h1 class="sub-header">Login</h1>
	{{ Form::open(array('url' => '/login', 'class' => 'form-horizontal', 'role' => 'form')) }}

	    {{ Form::label('username', 'Username:') }} <br>
	    {{ Form::text('username') }}<br><br>

	    {{ Form::label('password', 'Password:')}} <br>
	    {{ Form::password('password') }}<br><br>

	    {{ Form::submit('Login', array('class'=>'btn btn-primary')) }}

	{{ Form::close() }}
</div>


@stop