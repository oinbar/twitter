@extends('master')

@section('main-content')

<h1>Login</h1>


@if (Session::get('flash_message'))
	{{ Session::get('flash_message') }}
@endif

@foreach($errors->all() as $message) 
	<div class='error'>{{ $message }}</div>
@endforeach 

<br><br>

{{ Form::open(array('url' => '/login')) }}

    {{ Form::label('username', 'Username:')}}
    {{ Form::text('username') }}<br><br>

    {{ Form::label('password', 'Password:')}}
    {{ Form::password('password') }}<br><br>

    {{ Form::submit('Submit') }}

{{ Form::close() }}


@stop