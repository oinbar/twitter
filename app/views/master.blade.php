{{ HTML::style('/css/css.css'); }}
{{ HTML::style('/bootstrap/css/bootstrap.css'); }}

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Up2Data</title>
      {{-- TODO: add <link rel="icon" href="../../favicon.ico"> tags --}}

      {{ HTML::style('/ui1/bootstrap.min.css'); }}
      {{-- Custom css for this site --}}
      {{ HTML::style('/ui1/ui1_inside.css'); }}
      {{-- IE10 viewport hack for surface/desktop Windows 8 bug --}}
      {{ HTML::script('/ui1/ie10-viewport-bug-workaround.js'); }}
      {{-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries --}}
      <!--[if lt IE 9]>
        {{ HTML::script('/ui1/html5shiv_v3.7.2.min.js'); }}
        {{ HTML::script('/ui1/respond_v1.4.2.min.js'); }}
      <![endif]-->
   </head>

   <body>

	{{-- Top NavBar --}}
     <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
       <div class="container-fluid">
         <div class="navbar-header">
<!--            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
             <span class="sr-only">Toggle navigation</span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button> -->
           <a class="navbar-brand" href="/">Up2Data</a>
           <a class="navbar-brand" href="/about">About Us</a>
         </div>
         <div class="navbar-collapse collapse">           	
           
             @if (Auth::check())
             <ul class="nav navbar-nav navbar-right">             
	             <li>                                                 
		             <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>&nbsp; {{ Auth::user()->username; }} &nbsp;<span class="caret"></span></a>
		             <ul class="dropdown-menu" role="menu">
		               <li><a href="">Account Settings</a></li>
		               <li><a href="/logout">Log Out</a></li>
		             </ul>                    
	             </li>
             </ul> 
             @else
             <ul class="nav navbar-nav navbar-right">
	           <li><a href="/login">Login</a></li>
               <li><a href="/singup">Signup</a></li>
			 </ul>
             @endif

             		  
         </div>
       </div>
     </div>


     
     <div class="container-fluid">
       <div class="row">

   	@if (Auth::check())

	         <div class="col-sm-3 col-md-2 sidebar">
	           <ul class="nav nav-sidebar">
	              
	             {{-- Secondary NavBar --}}            
	             <li><a href="/feeds"> My Feeds </a></li>
				       <li><a href="#"> Reports </a></li>

	           </ul>           
	         </div>        		

	         	@yield('main-content')

		@else

				@yield('main-content')		

			<div class="col-sm-3 col-md-2 sidebar">

			</div>
		@endif

       </div>
     </div>



     {{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'); }}
     {{ HTML::script('ui1/bootstrap.min.js'); }}
     {{ HTML::script('ui1/docs.min.js'); }}
   </body>
</html>