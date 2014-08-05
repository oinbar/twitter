{{ HTML::style('/css/css.css'); }}
{{-- HTML::style('/bootstrap/css/bootstrap.css'); --}}

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>twitterintel</title>
   </head>
   <body>
     <div id="header" class="header">
     	@if (Auth::check())
	        <ul class="nav">
				<li><a href="/feeds" class="header-nav-a">My Feeds</a></li>
				<li><a href="#" class="header-nav-a">...</a></li>
				<li><a href="#" class="header-nav-a">...</a></li>
			</ul>
		@endif
		<ul class="nav-right">

			@if (Auth::check())
				<li><a href="/logout" class="header-nav-a">Logout as: {{ Auth::user()->username; }}</a></li>
			@else
				<li><a href="/signup" class="header-nav-a">Signup</a></li>
				<li><a href="/login" class="header-nav-a">Login</a></li>
				
			@endif

		</ul>
     </div>
      
     <div id=content class="container">
	     <div id="main-content" class="main-content">
	     	@yield('main-content') 
	     	
	     </div>
	
	     <div id="side-content" class="side-content">
	     	@yield('side-content') 
	     	
	     </div>
	 </div>

   </body>
</html>