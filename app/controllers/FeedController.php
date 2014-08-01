<?php

class FeedController extends BaseController {

	public function getFeeds (){

		$feeds = DB::connection('mongodb')->collection('data1')->where('type', 'feed')->get();
		return View::make('feeds')
			->with('feeds', $feeds);
	}

	public function getEditFeed ($feed_id = null) {

		if ($feed_id){	
			$feed = DB::connection('mongodb')->collection('data1')->where('_id', $feed_id)->first();		
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
	}

	public function postEditFeed () {
		$feed = array(
			'name' => Input::get('name'),
			'type' => 'feed',
			'update_rate' => Input::get('update_rate'),
			'feed_criteria' => Input::get('feed_criteria')
		);
		DB::connection('mongodb')->collection('data1')->insert($feed);
		return Redirect::to('feeds');
	}

	public function putEditFeed ($feed_id) {
		$feed = array(
			'name' => Input::get('name'),
			'update_rate' => Input::get('update_rate'),
			'feed_criteria' => Input::get('feed_criteria')
		);
		DB::connection('mongodb')->collection('data1')->where('_id', $feed_id)->update($feed);
		return Redirect::to('feeds');

	}

	public function getDeleteFeed ($feed_id) {
		DB::connection('mongodb')->collection('data1')->where('_id', $feed_id)->delete();
		return Redirect::to('feeds');
	}

	public function getViewFeed ($feed_id) {
		$data = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))->take(5)->get();
	    $count = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))->count();

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
	}

	public function getFetch ($feed_id) {
		Queue::push('QueueTasks@send_search_query', array('feed_id' => $feed_id));
		return 'pushed to the queue';

	}
}