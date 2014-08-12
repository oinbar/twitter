@extends('master')

@section('main-content')


@if (Session::get('flash_message'))
	{{ Session::get('flash_message') }}
@endif

@foreach($errors->all() as $message) 
	<div class='error'>{{ $message }}</div>
@endforeach 

<br><br>

{{ Form::open(array('url' => '/login', 'class' => 'well')) }}
	<h1>Login</h1>

    {{ Form::label('username', 'Username:') }} <br>
    {{ Form::text('username') }}<br><br>

    {{ Form::label('password', 'Password:')}} <br>
    {{ Form::password('password') }}<br><br>

    {{ Form::submit('Login', array('class'=>'btn btn-primary')) }}

{{ Form::close() }}


@stop