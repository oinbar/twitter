@extends('master')

@section('main-content')

    trends
	<?php
		$a = new AnalyticsController();
		$a->trends('1', '24', '5', 'hour');
		$a->trends('1', '2', '5', 'day');
	?>

@stop