<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>TwitTel</title>
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
           <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
             <span class="sr-only">Toggle navigation</span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>
           <a class="navbar-brand" href="#">TwitTel</a>
         </div>
         <div class="navbar-collapse collapse">
           
           <form class="navbar-form navbar-right">
             <input type="text" class="form-control" placeholder="deleteme">
           </form>
          
           <ul class="nav navbar-nav navbar-right">
             <li>
             <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>&nbsp;o.inbar&nbsp;<span class="caret"></span></a>
             <ul class="dropdown-menu" role="menu">
               <li><a href="#">Account Settings</a></li>
               <li><a href="#">Log Out</a></li>
             </ul>
             </li>
           </ul>
         </div>
       </div>
     </div>
 
     {{-- Below NavBar content. --}}
     <div class="container-fluid">
       <div class="row">
         <div class="col-sm-3 col-md-2 sidebar">
           <ul class="nav nav-sidebar">
             {{-- Left NavBar menu items --}}
             <li class="active"><a href="#">Overview</a></li>
             <li><a href="#">Feeds</a></li>
           </ul>
         </div>
         <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
           <h1 class="page-header">Overview</h1>
           <p class="lead"><i>**Some sort of graph/map goes here**</i></p>
           <div class="row placeholders">
             <div class="col-xs-6 col-sm-3 placeholder">
               <img data-src="holder.js/100x100/auto/industrial/text:yesterday" class="img-responsive" alt="Generic placeholder thumbnail">
               <h4>Label</h4>
               <span class="text-muted">Something else</span>
             </div>
             <div class="col-xs-6 col-sm-3 placeholder">
               <img data-src="holder.js/200x200/auto/social/text:today" class="img-responsive" alt="Generic placeholder thumbnail">
               <h4>Label</h4>
               <span class="text-muted">Something else</span>
             </div>
             <div class="col-xs-6 col-sm-3 placeholder">
               <img data-src="holder.js/150x150/auto/lava/text:tomorrow" class="img-responsive" alt="Generic placeholder thumbnail">
               <h4>Label</h4>
               <span class="text-muted">Something else</span>
             </div>
             <div class="col-xs-6 col-sm-3 placeholder">
               <img data-src="holder.js/180x180/auto/gray" class="img-responsive" alt="Generic placeholder thumbnail">
               <h4>Label</h4>
               <span class="text-muted">Something else</span>
             </div>
           </div>
           <p class="lead"><i>**end of overview page**</i></p>
           <ol class="breadcrumb">
             <li><a href="#">Feeds</a></li>
             <li class="active">Protest</li>
           </ol> 
           <h1 class="sub-header">Feed: <span style="opacity: 0.7">Protest</span></h1>
           <div class="table-responsive">
             <table class="table table-striped">
               <thead>
                 <tr>
                   <th>Score</th>
                   <th>Tweet</th>
                   <th>Location</th>
                   <th>Retweets</th>
                 </tr>
               </thead>
               <tbody>
                 <tr>
                   <td>3.4</td>
                   <td>RT @23Rikoon: Might be a good idea if BBC Scotland report the large protest outside their front door. Seems most peculiar if they don't.</td>
                   <td>Scottland</td>
                   <td>sit</td>
                 </tr>
                 <tr>
                   <td>5.4</td>
                   <td>consectetur</td>
                   <td>adipiscing</td>
                   <td>elit</td>
                 </tr>
                 <tr>
                   <td>4.6</td>
                   <td>nec</td>
                   <td>odio</td>
                   <td>Praesent</td>
                 </tr>
                 <tr>
                   <td>3.4</td>
                   <td>Sed</td>
                   <td>cursus</td>
                   <td>ante</td>
                 </tr>
                 <tr>
                   <td>2.6</td>
                   <td>diam</td>
                   <td>Sed</td>
                   <td>nisi</td>
                 </tr>
               </tbody>
             </table>
           </div>
           <ul class="pagination">
             <li class="disabled"><a href="#">&laquo;</a></li>
             <li class="active"><a href="#">1<span class="sr-only">(current)</span></a></li>
             <li><a href="#">2</a></li>
             <li><a href="#">3</a></li>
             <li><a href="#">4</a></li>
             <li><a href="#">5</a></li>
             <li><a href="#">6</a></li>
             <li><a href="#">7</a></li>
             <li><a href="#">8</a></li>
             <li><a href="#">&raquo;</a></li>
           </ul>
           <p class="lead"><i>**end of overview page**</i></p>
           <h1 class="sub-header">Create Feed</h1>
           <form class="form-horizontal" role="form">
             <div class="form-group">
               <label for="inputFeedName" class="col-sm-2 control-label">Name</label>
               <div class="col-sm-9 col-lg-6">
                 <input type="text" class="form-control" id="inputFeedName" placeholder="Name">
               </div>
             </div>
             <div class="form-group">
               <label for="inputCriteria" class="col-sm-2 control-label">Criteria</label>
               <div class="col-sm-9 col-lg-6">
                 <textarea class="form-control col-sm-9 col-lg-6" id="inputCriteria"></textarea>
               </div>
             </div>
             <div class="form-group">
               <label for="updateRate" class="col-sm-2 control-label">Update rate</label>
               <div class="btn-group col-sm-9" data-toggle="buttons" id="updateRate">
                 <label class="btn btn-default active">
                   <input type="radio" name="options" id="rate-hourly" checked> hourly
                 </label>
                 <label class="btn btn-default">
                   <input type="radio" name="options" id="rate-daily"> daily
                 </label>
                 <label class="btn btn-default">
                   <input type="radio" name="options" id="rate-weekly"> weekly
                 </label>
               </div>
             </div>
             <div class="form-group">
               <label for="feedEnabled" class="col-sm-2 control-label">Feed status</label>
               <div class="btn-group col-sm-9" data-toggle="buttons" id="feedEnabled">
                 <label class="btn btn-xs btn-default active">
                   <input type="radio" name="options" id="feed-enabled" checked> enabled 
                 </label>
                 <label class="btn btn-xs btn-default">
                   <input type="radio" name="options" id="feed-disabled"> disabled 
                 </label>
               </div>
             </div>
             <div class="form-group">
               <div class="col-sm-offset-2 col-sm-10">
                 <div class="checkbox">
                   <label>
                     <input type="checkbox"> Remember me
                   </label>
                 </div>
               </div>
             </div>
             <div class="form-group">
               <div class="col-sm-offset-2 col-sm-10">
                 <button type="submit" class="btn btn-default">Create</button>
               </div>
             </div>
           </form>
 
         </div>
       </div>
     </div>


        {{--
	    <div id="main-content" class="main-content">
	     	@yield('main-content') 
	     	
	    </div>
	
	    <div id="side-content" class="side-content">
	     	@yield('side-content') 
	     	
	    </div>
	 </div>
     --}}

     {{ HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'); }}
     {{ HTML::script('ui1/bootstrap.min.js'); }}
     {{ HTML::script('ui1/docs.min.js'); }}
   </body>

</html>
