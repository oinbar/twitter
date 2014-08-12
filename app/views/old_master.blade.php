{{ HTML::style('/css/css.css'); }}
<!-- {{ HTML::style('/bootstrap/css/bootstrap.css'); }} -->

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>twitterintel</title>
   </head>
   <body>
     <div id="header" class="header">
     	
	        <ul class="nav">
				@if (Auth::check())
					<li><a href="/myfeeds" class="header-nav-a">My Feeds</a></li>
				@endif
				<li><a href="/about" class="header-nav-a">About</a></li>				
			</ul>
		
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