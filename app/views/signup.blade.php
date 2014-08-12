@extends('master')

@section('main-content')

@foreach($errors->all() as $message) 
	<div class='error'>{{ $message }}</div>
@endforeach <br>

{{ Form::open(array('url' => '/signup', 'class' => 'well')) }}

	<h1>Sign up</h1>

    {{ Form::label('username', 'Username:')}}<br>
    {{ Form::text('username') }}<br><br>

    {{ Form::label('password', 'Password:')}}<br>
    {{ Form::password('password') }}<br><br>

    {{ Form::label('email', 'Email:')}}<br>
    {{ Form::text('email') }}<br><br>

    {{ Form::submit('Submit', array('class'=>'btn btn-primary')) }}

{{ Form::close() }}

@stop