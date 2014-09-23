@extends('master')

@section('main-content')

 
   <h1 class="page-header">Feed: Protest > Analytics </h1>
   <p class="lead"><i>Here's whats going on in your neck of the woods:</i></p>
   
   <p class="text-muted">The hourly trend over the past 24 hours:</p>  
   <img src="/images/{{ $protest_hour_trend }}" alt="analytics_img_hour">
   

   <img src="/images/{{ $protest_day_trend }}" alt="analytics_img_day">
   <span class="text-muted">The daily trend over the past 7 days:</span>



@stop