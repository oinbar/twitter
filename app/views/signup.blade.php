@extends('master')

@section('main-content')

<h1>Sign up</h1>

{{ Form::open(array('url' => '/signup')) }}

    Username:<br>
    {{ Form::text('username') }}<br><br>

    Password:<br>
    {{ Form::password('password') }}<br><br>

    Email<br>
    {{ Form::text('email') }}<br><br>

    {{ Form::submit('Submit') }}

{{ Form::close() }}


@stop