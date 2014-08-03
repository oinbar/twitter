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

Route::post('/edit_feed', array('before' => 'csrf', 'uses' => 'FeedController@postEditFeed'));

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

Route::get('/signup', array('before' => 'guest', 'uses' => 'UserController@getSignup'));

Route::post('/signup', array('before' => 'csrf', 'uses' => 'UserController@postSignup'));

Route::get('/login', array('before' => 'guest', 'uses' => 'UserController@getLogin'));

Route::post('/login', array('before' => 'csrf', 'uses' => 'UserController@postLogin'));

Route::get('/logout', array('before' => 'auth', 'uses' => 'UserController@getLogout'));

Route::get('/debug', 'AdminController@debug');

Route::get('/test', 'AdminController@test');

Route::get('/startqueue', function(){
	Artisan::call('queue:listen');
	return Redirect::to('/');

});

