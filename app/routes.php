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

Route::get('/feeds', 'FeedController@getFeeds'); 

Route::get('/edit_feed/{feed_id?}', 'FeedController@getEditFeed');

Route::post('/edit_feed', 'FeedController@postEditFeed');

Route::put('/edit_feed/{feed_id}', 'FeedController@putEditFeed');

Route::get('/delete_feed/{feed_id}', 'FeedController@getDeleteFeed');

Route::get('/view_feed/{feed_id}', 'FeedController@getViewFeed');

Route::get('/fetch/{feed_id}', 'FeedController@getFetch');

Route::get('/feed/{feed_id?}/{start?}/{end?}', function($feed_id = null,
														$start= 0,
														$end = 100) {
	return View::make('feed')	
	->with('feed_id', $feed_id)
	->with('start', $start)
	->with('end', $end);
});

Route::get('/debug', 'AdminController@debug');

Route::get('/test', 'AdminController@test');

?>