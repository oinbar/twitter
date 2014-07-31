<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function() {
	return View::make('hello');
});

Route::get('/feeds', function() {
	$feeds = DB::collection('data1')->where('type', 'feed')->get();
	return View::make('feeds')
	->with('feeds', $feeds);
});

Route::get('/edit_feed/{feed_id?}', function($feed_id = null) {
	if ($feed_id){	
		$feed = DB::collection('data1')->where('_id', $feed_id)->first();		
		return View::make('edit_feed')
			->with('feed_id', $feed['_id'])
			->with('name', $feed['name'])
			->with('feed_criteria', $feed['feed_criteria'])
			->with('update_rate', $feed['update_rate'])
			->with('method', 'put');				
	}
	else {
		return View::make('edit_feed')
			->with('feed_id', '')
			->with('name', '')
			->with('feed_criteria', '')
			->with('update_rate', '')
			->with('method', 'post');		
	}	
});

Route::post('/edit_feed', function(){
	$feed = array(
		'name' => Input::get('name'),
		'type' => 'feed',
		'update_rate' => Input::get('update_rate'),
		'feed_criteria' => Input::get('feed_criteria')
	);
	DB::collection('data1')->insert($feed);
	return Redirect::to('feeds');
});

Route::put('/edit_feed/{feed_id}', function($feed_id){
	$feed = array(
		'name' => Input::get('name'),
		'update_rate' => Input::get('update_rate'),
		'feed_criteria' => Input::get('feed_criteria')
	);
	DB::collection('data1')->where('_id', $feed_id)->update($feed);
	return Redirect::to('feeds');
});

Route::get('/delete_feed/{feed_id}', function($feed_id){
	DB::collection('data1')->where('_id', $feed_id)->delete();
	return Redirect::to('feeds');
});

Route::get('/view_feed/{feed_id}', function($feed_id){	
	$data = DB::collection('data1')->whereIn('feeds', array($feed_id))->take(5)->get();
    $count = DB::collection('data1')->whereIn('feeds', array($feed_id))->count();

	$statuses = array();
	if (!empty($data)){
		foreach ($data as $status) {
			array_push($statuses, $status['created_at'] . ' - ' . 
								  $status['user']['name'] . ' - ' . 
					   			  $status['user']['location'] . ' - ' .
 					   			  $status['text']);		
		}
	}

	return View::make('feed')
	->with('num_records', $count)
	->with('statuses', $statuses)
	->with('feed_id', $feed_id)
	->with('start', '123')
	->with('end', '123');
});


 Route::get('/fetch/{feed_id}', function($feed_id){
	Queue::push('QueueTasks@send_search_query', array('feed_id' => $feed_id));
	return 'pushed to the queue';
});

Route::get('/feed/{feed_id?}/{start?}/{end?}', function($feed_id = null,
														$start= 0,
														$end = 100) {
	return View::make('feed')	
	->with('feed_id', $feed_id)
	->with('start', $start)
	->with('end', $end);
});



Route::get('/debug', function() {

    echo '<pre>';

    echo '<h1>environment.php</h1>';
    $path   = base_path().'/environment.php';

    try {
        $contents = 'Contents: '.File::getRequire($path);
        $exists = 'Yes';
    }
    catch (Exception $e) {
        $exists = 'No. Defaulting to `production`';
        $contents = '';
    }


    echo "Checking for: ".$path.'<br>';
    echo 'Exists: '.$exists.'<br>';
    echo $contents;
    echo '<br>';



    echo '<h1>Environment</h1>';
    echo App::environment().'</h1>';

    echo '<h1>Debugging?</h1>';
    if(Config::get('app.debug')) echo "Yes"; else echo "No";


    echo '<h1>Database Config</h1>';
    print_r(Config::get('database.connections.mongodb'));

    echo '<h1>Test Database Connection</h1>';

    try {
        //$results = DB::select('SHOW DATABASES;');
        $results = DB::collection('data1');
        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
        echo "<br><br>Your Databases:<br><br>";
        print_r($results);
    } 
    catch (Exception $e) {
        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
    }

    echo '</pre>';

});

Route::get('/test', function(){
		$feed_id = 'israel_feed';

		require_once 'twitter-api-php/TwitterAPIExchange.php';
		$settings = array(
		    'oauth_access_token' => "2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC",
		    'oauth_access_token_secret' => "sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk",
		    'consumer_key' => "x393VwuVLnnixX6Ld7panxSp8",
		    'consumer_secret' => "qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u"
		);
		// GET THE SEARCH CRITERIA FROM THE DB TO ADD INTO THE QUERY
		$getfield = '?count=100&q=israel' . urlencode(DB::collection('data1')->where('_id', $feed_id)->first()['feed_criteria']);

		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
		$json = $twitter->setGetfield($getfield)
		             ->buildOauth($url, $requestMethod)
		             ->performRequest();	             
		
		// LOOP OVER THE RESULTS COLLECTION
		$data = json_decode($json, true);

		foreach ($data['statuses'] as $status){		
			//ADD MONGOID
			$status['_id'] = $status['id'];
			$db_record = DB::collection('data1')->where('_id', $status['_id'])->first();	
			if ($db_record) {
				// ADD REFERENCE TO FEED AND UPDATE RECORD
				array_push($db_record['feeds'], $feed_id);
				$db_record['feeds'] = array_unique($db_record['feeds']);
				DB::collection('data1')->where('_id', $status['_id'])->update($db_record);
			} else {						
				// ADD REFERENCE TO FEED AND INSERT RECORD INTO DB
				$status['feeds'] = array($feed_id);			
				DB::collection('data1')->insert($status);	
			}
		}
	});

?>