@extends('master')

@section('main-content')

{{ $protest_hour_trend }}

<img src="{{ $protest_hour_trend }}" alt="protest_hour_trend" style="width:304px;height:228px">
<img src="{{ $protest_day_trend }}" alt="protest_day_trend" style="width:304px;height:228px">


@stop