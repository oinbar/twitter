@extends('master')

@section('main-content')

<div class="inner-container">
	<center> Feed: {{ $feed_id }} </center>
	<center> Results: {{ $start }} - {{ $end }} </center>
</div>

<div>
    <ul class="feeds-items">
		<li><a href="#">Post1 </a></li>
		
		<?php
		foreach($statuses as $status) {
			echo '<li><a href="#"> '. $status. ' </a></li>';
		}
		?>
	</ul>
</div>


@stop