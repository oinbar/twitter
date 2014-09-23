@extends('master')

@section('main-content')

	hour trends
	<?php
		$im = imagecreatefrompng($protest_hour_trend);
		header('Content-Type: image/png');
		imagepng($im);
		unset($protest_hour_trend);
	?>

	day trend
	<?php
		$im = imagecreatefrompng($protest_day_trend);
		header('Content-Type: image/png');
		imagepng($im);
		unset($protest_day_trend);
	?>

@stop