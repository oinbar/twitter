@extends('master')

@section('main-content')

	<img src="{{ $protest_hour_trend }}">
	<?php
		header('Content-Type: image/png');
		imagepng($protest_hour_trend);

	?>

@stop