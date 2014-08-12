{{ HTML::style('/css/css.css'); }}
{{ HTML::style('/bootstrap/css/bootstrap.css'); }}

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>twitterintel</title>
   </head>
   <body>

      
     <div id=content class="container">

     	<nav class="nav nav-pills header" role="navigation">     
	        <ul class="nav nav-pills pull-left">
	        	<li class="{{ $active_my_feeds = '' }}" ><a href="/about">About</a></li>				
				@if (Auth::check())
					<li class= "{{ $active_my_feeds = '' }}" ><a href="/feeds">My Feeds</a></li>
				@endif			
			</ul>
			
			<ul class="nav nav-pills pull-right">

				@if (Auth::check())
					<li><a href="/logout">Logout as: {{ Auth::user()->username; }}</a></li>
				@else
					<li><a href="/signup">Signup</a></li>
					<li><a href="/login">Login</a></li>
					
				@endif
			</ul>	
     	</nav>


	    <div id="main-content" class="main-content">
	     	@yield('main-content') 
	     	
	    </div>
	
	    <div id="side-content" class="side-content">
	     	@yield('side-content') 
	     	
	    </div>
	 </div>

	 <script> src="/bootstrap/js/bootstrap.js"</script>
   </body>
</html>