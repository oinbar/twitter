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
	return View::make('master');
});

Route::get('/feeds/', 'FeedController@getFeeds'); 

Route::get('/edit_feed/{feed_id?}', array('before' => 'has_feed', 'uses' => 'FeedController@getEditFeed'));

Route::post('/edit_feed', array('before' => 'csrf', 'before' => 'has_feed', 'uses' => 'FeedController@postEditFeed'));

Route::put('/edit_feed/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@putEditFeed'));

Route::get('/delete_feed/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@getDeleteFeed'));

Route::get('/view_feed/{feed_id}/{skip?}', array('before' => 'has_feed', 'uses' => 'FeedController@getViewFeed'));

Route::get('/startfetch/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@startFetching'));

Route::get('/stopfetch/{feed_id}', array('before' => 'has_feed', 'uses' => 'FeedController@stopFetching'));


Route::get('/signup', array('before' => 'guest', 'uses' => 'UserController@getSignup'));

Route::post('/signup', array('before' => 'csrf', 'uses' => 'UserController@postSignup'));

Route::get('/login', array('before' => 'guest', 'uses' => 'UserController@getLogin'));

Route::post('/login', array('before' => 'csrf', 'uses' => 'UserController@postLogin'));

Route::get('/logout', array('before' => 'auth', 'uses' => 'UserController@getLogout'));


Route::get('/debug', 'AdminController@debug');


Route::get('/queue/send', function(){
	$data['string'] = 'hello world';
	Queue::push(function($job, $data) {
		File::append(app_path().'/queue.txt', $data['string'].PHP_EOL);
		$job->delete();
	});
	return 'OK';
});

Route::post('/queue/push', function(){ 
	return Queue::marshall(); 
	});

Route::get('/test', 'AdminController@test');

Route::get('/test2', 'AdminController@test2');

Route::get('/startqueue', 'AdminController@check_start_queue_listener');

