{{ HTML::style('/css/css.css'); }}

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>twitterintel</title>
   </head>
   <body>
     <div id="header" class="header">
        <ul class="nav">
			<li><a href="/feeds">My Feeds</a></li>
			<li><a href="#">...</a></li>
			<li><a href="#">...</a></li>
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