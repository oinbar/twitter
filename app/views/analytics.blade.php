@extends('master')

@section('main-content')

 
   <h1 class="page-header">Feed: Protest > Analytics </h1>
   <p class="lead"><i>Here's whats going on in your neck of the woods:</i></p>
   <div class="row placeholders">
     <div class="col-xs-6 col-sm-3 placeholder">
       <img src="/images/{{ $protest_hour_trend }}" alt="analytics_img">
       <h4>Label</h4>
       <span class="text-muted">Something else</span>
     </div>
     <!-- <div class="col-xs-6 col-sm-3 placeholder">
       <img data-src="holder.js/100x100/auto/industrial/text:yesterday" class="img-responsive" alt="Generic placeholder thumbnail">
       <h4>Label</h4>
       <span class="text-muted">Something else</span>
     </div> -->
   </div>



@stop