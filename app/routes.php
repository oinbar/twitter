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
	return View::make('home');
});

Route::get('/phpinfo', function() {
	echo phpinfo();
});

Route::get('/feeds/', 'FeedController@getFeeds'); 

Route::get('/about', function(){ return View::make('about'); });

Route::get('/edit_feed/{feed_id?}', array('before' => 'has_feed', 'uses' => 'FeedController@getEditFeed'));

Route::post('/edit_feed/{feed_id?}', array('before' => 'csrf', 'before' => 'has_feed', 'uses' => 'FeedController@postEditFeed'));

Route::put('/edit_feed/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@putEditFeed'));

Route::get('/delete_feed/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@getDeleteFeed'));

Route::get('/getFeedStatus/{feed_id}', array('uses' => 'FeedController@getFeedStatus'));

Route::get('/get_feed_data_json/{feed_id}/{page_num}/{results_per_page?}', array('before' => 'has_feed', 'uses' => 'FeedController@getFeedDataJson'));





Route::get('/startfetch/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@startFetching'));

Route::get('/stopfetch/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@stopFetching'));

Route::get('/tweet/{id}', array('uses' => 'FeedController@showTweet')); 


Route::get('/view_feed/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@getViewFeed'));

Route::get('/alerts/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@getAlerts'));

Route::get('/view_feed_data/{feed_id}/{skip?}', array('before' => 'has_feed', 'uses' => 'FeedController@getViewFeedData'));

Route::get('/get_trends_data/{feed_id}/{timepoints?}/{num_features?}/{timeframe?}/{make_lower_case?}', array('before' => 'has_feed', 'uses' => 'AnalyticsController@getTrendsData'));

Route::get('get_alerts_data/{feed_id}', array('before' => 'has_feed', 'uses' => 'AnalyticsController@getAlertsData'));



Route::get('/trends/{feed_id}/{timepoints?}/{num_features?}/{timeframe?}', array('uses' => 'AnalyticsController@trends'));

Route::get('/analytics/', array('uses' => 'AnalyticsController@showAnalytics'));

Route::get('/images/{file}', array('uses' => 'AdminController@getImage'));


Route::get('/signup', array('before' => 'guest', 'uses' => 'UserController@getSignup'));

Route::post('/signup', array('before' => 'csrf', 'uses' => 'UserController@postSignup'));

Route::get('/login', array('before' => 'guest', 'uses' => 'UserController@getLogin'));

Route::post('/login', array('before' => 'csrf', 'uses' => 'UserController@postLogin'));

Route::get('/logout', array('before' => 'auth', 'uses' => 'UserController@getLogout'));

Route::get('/queue/send', function(){

	$feed_id = 2;
	$data['feed_id'] = $feed_id;

	Queue::push('QueueTasks@send_search_query', array('feed_id' => $feed_id));

	$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;

	if ($feed_status == 'on') {
		sleep(10);
		return Redirect::to('/queue/send');
	}
});

Route::post('/queue/push', function(){
	return Queue::marshal(); 
});

Route::get('/mongoquery', 'AdminController@mongoQuery');

Route::get('/cacheview', 'AdminController@cacheView');

Route::get('/debug', 'AdminController@debug');

Route::get('/test', 'AdminController@test');

Route::get('/test2', 'AdminController@test2');

Route::get('/loadqueues', 'AdminController@load_queues');

Route::get('/startqueues', 'AdminController@start_queue_listeners');

