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

require "twitter_fetch.php";

Route::get('/', function() {
	return View::make('hello');
});

Route::get('/feeds', function() {
	return View::make('feeds');
});

Route::get('/edit_feed', function() {
	return View::make('edit_feed');
});

Route::get('/feed/{feed_id?}/{start?}/{end?}', function($feed_id = null,
														$start= 0,
														$end = 100) {
	return View::make('feed')	
	->with('feed_id', $feed_id)
	->with('start', $start)
	->with('end', $end);
}); 

Route::get('/twitter_test', function() {
	
	require_once('twitter-api-php/TwitterAPIExchange.php');
	$settings = array(
	    'oauth_access_token' => "2492151342-mRMDlwJGaij2yZQB5CHyU2FbaymXnIcEhYnhcgC",
	    'oauth_access_token_secret' => "sDCCPbYt39Uii76de2HcSMbcTFffby1BwxjAEheL6b4dk",
	    'consumer_key' => "x393VwuVLnnixX6Ld7panxSp8",
	    'consumer_secret' => "qglHdDR9gcwpyhdFSF37hPpMwXSrIchkmp9DV8TZ8iOzLNt95u"
	);
	
	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$getfield = '?q=israel';
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	$json = $twitter->setGetfield($getfield)
	             ->buildOauth($url, $requestMethod)
	             ->performRequest();	             
	              	
	$data = json_decode($json, true);
	$i = 0;
	foreach ($data['statuses'] as $status){
		$statuses[$i] = $status['text'];
		$i++;
	}
	
	return View::make('feed')
	->with('statuses', $statuses)
	->with('feed_id', '123')
	->with('start', '123')
	->with('end', '123');



});

?>