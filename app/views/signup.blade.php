@extends('master')

@section('main-content')

<h1>Sign up</h1>

@foreach($errors->all() as $message) 
	<div class='error'>{{ $message }}</div>
@endforeach <br>

{{ Form::open(array('url' => '/signup')) }}

    {{ Form::label('username', 'Username:')}}
    {{ Form::text('username') }}<br><br>

    {{ Form::label('password', 'Password:')}}
    {{ Form::password('password') }}<br><br>

    {{ Form::label('email', 'Email:')}}
    {{ Form::text('email') }}<br><br>

    {{ Form::submit('Submit') }}

{{ Form::close() }}

@stop