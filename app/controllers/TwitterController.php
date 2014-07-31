<?php

class TwitterController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function send_search_query($feed_id) {
		require_once __DIR__.'/../twitter-api-php/TwitterAPIExchange.php';
		$settings = array(
		    'oauth_access_token' => "2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC",
		    'oauth_access_token_secret' => "sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk",
		    'consumer_key' => "x393VwuVLnnixX6Ld7panxSp8",
		    'consumer_secret' => "qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u"
		);
		// GET THE SEARCH CRITERIA FROM THE DB TO ADD INTO THE QUERY
		$getfield = '?count=100&q=' . urlencode(DB::collection('data1')->where('_id', $feed_id)->first()['feed_criteria']);

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
				unset($db_record['_id']); // remove _id so it does not try to update mongo id
				DB::collection('data1')->where('_id', $status['_id'])->update($db_record);
			} else {						
				// ADD REFERENCE TO FEED AND INSERT RECORD INTO DB
				$status['feeds'] = array($feed_id);			
				DB::collection('data1')->insert($status);			
			} 
		}
	}
}
