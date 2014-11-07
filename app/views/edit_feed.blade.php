@extends('master')

@section('main-content')

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h2>
		@if ($new_feed)
			Create new feed
		@else
			Edit feed: {{$name}}
		@endif
	</h2>

	<h3>Feed type/intializer: Twitter</h3>

	{{ Form::open(array('url'=> '/edit_feed/' . $feed_id, 
						'method' => 'POST',
						'accept-charset' => 'ISO-8859-1')) }}

	{{ Form::label('status', 'Status: ')}}<br>
	{{ Form::select('status', array('off'=>'off',
									'on'=>'on'), $status); }}<br><br>

	{{ Form::label('name', 'Feed Name: ') }} <br>
	{{ Form::text('name', $name) }} <br><br>

	{{ Form::label('params', 'params: ') }} <br>
	{{ Form::textarea('params', $params) }} <br><br>

	{{--{{ Form::label('update_rate', 'Update Rate: ')}}<br>--}}
	{{--{{ Form::select('update_rate', array('hourly'=>'hourly', --}}
										 {{--'daily'=>'daily', --}}
										 {{--'weekly'=>'weekly'), $update_rate); }}<br><br>--}}

	{{ Form::label('status', 'Status: ')}}<br>
	{{ Form::select('status', array('off'=>'off',
									'on'=>'on'), $status); }}<br><br>


    <h3>Transformations:</h3>

    {{ form::checkbox('calais', 'calais', $calais) }}
    {{ form::label('calais', 'open calais metadata') }}<br>
    {{ form::label('calais_params', 'params') }}
    {{ form::text('calais_params', $calais_params) }}<br>

    {{ form::checkbox('sutime', 'sutime', $sutime) }}
    {{ form::label('sutime', 'time extraction') }}<br>
    {{ form::label('sutime_params', 'params') }}
    {{ form::text('sutime_params', $sutime_params) }}<br>


    <h3>Views:</h3>

    {{ form::checkbox('data_overview', 'data_overview', $data_overview) }}
    {{ form::label('data_overview', 'data overview') }}<br>
    {{ form::label('data_overview_params', 'params') }}
    {{ form::text('data_overview_params', $data_overview_params) }}<br>

    {{ form::checkbox('alerts_overview', 'alerts_overview', $alerts_overview) }}
    {{ form::label('alerts_overview', 'alerts overview') }}<br>
    {{ form::label('alerts_overview_params', 'params') }}
    {{ form::text('alerts_overview_params', $alerts_overview_params) }}<br>

    {{ form::checkbox('alerts_timeline', 'alerts_timeline', $alerts_timeline) }}
    {{ form::label('alerts_timeline', 'alerts timeline') }}<br>
    {{ form::label('alerts_timeline_params', 'params') }}
    {{ form::text('alerts_timeline_params', $alerts_timeline_params) }}<br>

    <br><br>
	{{ form::submit('Submit')}}
	{{ Form::close() }}

</div>

@stop
