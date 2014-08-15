<?php

class ProcessingTasks extends BaseController {

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

		// LOOP OVER THE RESULTS COLLECTION, AND STORE IN CACHE FOR DATA PIPELINE
		$data = json_decode($json, true);
		$redis = Redis::connection();

		foreach ($data['statuses'] as $status){				
			$status['_id'] = $status['id']; //add mongoID
			$status['feeds'] = array($feed_id); //add reference to feed						
			$redis->rpush('pending_calais', json_encode($status));		
		} 		
	}

	public function cache_to_db ($cache_list = 'pending_persistence', $batch_size = 1000) {

		$redis = Redis::connection();
		$batch_size = min($redis->llen($cache_list), $batch_size);		

		for ($i = 0; $i <= $batch_size; $i++) {
			try{
				$record = json_decode($redis->lpop($cache_list), true);
				$db_record = DB::connection('mongodb')->collection('data1')->where('_id', $record['_id'])->first();
				if ($db_record) {
					// IF ALREADY EXISTS IN DB COMBINE REFERENCES TO FEED AND UPDATE RECORD
					$record['feeds'] = array_merge($db_record['feeds'], $record['feeds']);				
					$record['feeds'] = array_unique($record['feeds']);
					unset($record['_id']); // remove _id so it does not try to update mongo id
					DB::connection('mongodb')->collection('data1')->where('_id', $db_record['_id'])->update($record);
				} 
				else {
					DB::connection('mongodb')->collection('data1')->insert($record);
				}
			} catch (Exception $e) {
				Log::error($e);
			}
		}		
	}

	public function send_tweet_to_calais ($cache_list = 'pending_calais', $batch_size = 1000) {
		
		$redis = Redis::connection();
		$batch_size = min($redis->llen($cache_list), $batch_size);
		$calais_key = 'qupquc5c4qzj7sg9knu5ad4w';


	}
}
