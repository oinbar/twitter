	<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />

 	<script>
    	Timeline_ajax_url="http://api.simile-widgets.org/ajax/2.2.1/simile-ajax-api.js";
        Timeline_urlPrefix='http://api.simile-widgets.org/timeline/2.3.1/';
        Timeline_parameters='bundle=true';
 	</script>

	<script src="http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js" type="text/javascript"></script>
	<script src="/ui1/jquery.min.js"></script>

    </head>

@extends('master')

@section('main-content')

@include('feed_data')

@include('feed_trends')

@include('timeline')

@stop


