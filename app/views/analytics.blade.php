@extends('master')

@section('main-content')

	<?php
		header('Content-Type: image/png');
		imagepng($protest_hour_trend);

	?>

@stop