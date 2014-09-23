@extends('master')

@section('main-content')

 <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
   <h1 class="page-header">Feed: Protest > Analytics </h1>
   <p class="lead"><i>Here's whats going on in your neck of the woods:</i></p>
   
   <p class="lead">The hourly trend over the past 24 hours:</p>  
   <img src="/images/{{ $protest_hour_trend }}" alt="analytics_img_hour"><br>
   

   <p class="lead">The daily trend over the past 7 days:</p>  
   <img src="/images/{{ $protest_day_trend }}" alt="analytics_img_day">
 </div>


@stop