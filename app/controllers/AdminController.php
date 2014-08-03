<?php

class AdminController extends BaseController {

	public function debug() {

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
	        $results = DB::connection('mongodb')->collection('data1');
	        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
	        //echo "<br><br>Your Databases:<br><br>";
	        //print_r($results);
	    } 
	    catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }

	    echo '<h1>Database Config</h1>';
	    print_r(Config::get('database.connections.mysql'));

	    echo '<h1>Test Database Connection</h1>';

	    try {
	        $results = DB::connection('mysql')->select('SHOW DATABASES;');
	        echo '<strong style="background-color:green; padding:5px;">Connection confirmed</strong>';
	        echo "<br><br>Your Databases:<br><br>";
	        print_r($results);
	    } 
	    catch (Exception $e) {
	        echo '<strong style="background-color:crimson; padding:5px;">Caught exception: ', $e->getMessage(), "</strong>\n";
	    }
	    echo '</pre>';
	}

	public function test() {
				
		$feed_id = '1';

		include __DIR__.'/../twitter-api-php/TwitterAPIExchange.php';

		$settings = array(
		    'oauth_access_token' => "2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC",
		    'oauth_access_token_secret' => "sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk",
		    'consumer_key' => "x393VwuVLnnixX6Ld7panxSp8",
		    'consumer_secret' => "qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u"
		);
		// GET THE SEARCH CRITERIA FROM THE DB TO ADD INTO THE QUERY
		$getfield = '?count=100&q=' . urlencode(DB::connection('mysql')
										->table('feeds')->where('id', $feed_id)->orderBy('created_at', 'desc')
										->first()->criteria);
		


		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
		$json = $twitter->setGetfield($getfield)
		             ->buildOauth($url, $requestMethod)
		             ->performRequest();	             
		
		// LOOP OVER THE RESULTS COLLECTION
		$data = json_decode($json, true);

		
		foreach ($data['statuses'] as $status){		

			echo Pre::render($status);

			//ADD MONGOID
			$status['_id'] = $status['id'];
			$db_record = DB::connection('mongodb')->collection('data1')->where('_id', $status['_id'])->first();
			if ($db_record) {
				// ADD REFERENCE TO FEED AND UPDATE RECORD
				array_push($db_record['feeds'], $feed_id);
				$db_record['feeds'] = array_unique($db_record['feeds']);
				unset($db_record['_id']); // remove _id so it does not try to update mongo id
				DB::connection('mongodb')->collection('data1')->where('_id', $status['_id'])->update($db_record);
			} else {						
				// ADD REFERENCE TO FEED AND INSERT RECORD INTO DB
				$status['feeds'] = array($feed_id);			
				DB::connection('mongodb')->collection('data1')->insert($status);			
			} 
		}
	}
}
