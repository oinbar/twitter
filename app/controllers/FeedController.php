<?php

class FeedController extends BaseController {

	// public function getFeeds (){

	// 	$feeds = DB::connection('mongodb')->collection('data1')->where('type', 'feed')->get();
	// 	return View::make('feeds')
	// 		->with('feeds', $feeds);
	// }

	public function getFeeds () {
		$feeds = DB::connection('mysql')->table('users_feeds')->where('user_id', Auth::user()->id)
			->select('feed_id','feed_name')->get();

		return View::make('feeds')
			->with('feeds', $feeds);
	}

	public function getEditFeed ($feed_id = null) {

		if ($feed_id){	
			$feed1 = DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->first();		
			$feed2 = DB::connection('mysql')->table('feeds')
				->where('id', $feed_id)
				->orderBy('created_at', 'desc')->first();

			return View::make('edit_feed')
				->with('feed_id', $feed1->feed_id)
				->with('name', $feed1->feed_name)
				->with('status', $feed1->feed_status)
				->with('criteria', $feed2->criteria)
				->with('update_rate', $feed2->update_rate)				
				->with('method', 'put');				
		}
		else {
			return View::make('edit_feed')
				->with('feed_id', '')
				->with('name', '')
				->with('status', '')
				->with('criteria', '')
				->with('update_rate', '')
				->with('method', 'post');		
		}				
	}

	public function postEditFeed () {
		$id = DB::connection('mysql')->table('users_feeds')->insertGetId(array(
			'user_id' => Auth::user()->id,
			'feed_name' => Input::get('name'),
			'feed_status' => 'on'));
		DB::connection('mysql')->table('feeds')->insert(array(
			'id' => $id,
			'update_rate' => Input::get('update_rate'),
			'criteria' => Input::get('criteria'),
			'created_at' => new DateTime));
		return Redirect::to('/feeds');
	}

	public function putEditFeed ($feed_id) {
		DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->update(array(
			'feed_name' => Input::get('name'),
			'feed_status' => Input::get('status')
		));
		DB::connection('mysql')->table('feeds')->where('id', $feed_id)->insert(array(
			'id' => $feed_id,
			'update_rate' => Input::get('update_rate'),
			'criteria' => Input::get('criteria'),
			'created_at' => new DateTime
		));
		return Redirect::to('feeds');
	}

	public function getDeleteFeed ($feed_id) {
		DB::connection('mongodb')->collection('data1')->where('_id', $feed_id)->delete();
		return Redirect::to('feeds');
	}

	public function getViewFeed ($feed_id, $skip = 0) {
		$take = 20; // numbe of results to select
		$data = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))->skip($skip)->take($take)->get();
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

		$feed = DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->first();
		
		return View::make('feed')
		->with('feed', $feed)
		->with('num_records', $count)
		->with('statuses', $statuses)
		->with('feed_id', $feed_id)
		->with('start', $skip)
		->with('end', $skip + $take)
		->with('prev', max(0, $skip-$take));
	}

	public function startFetching ($feed_id) {

		//turn feed status on
		DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->update(array('feed_status' => 'on'));

		//push twitter search task to the queue
		Queue::push('QueueTasks@send_search_query', array('feed_id' => $feed_id));
		// Queue::push(function($job) use ($feed_id){
		// 	$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;
		// 	if ($feed_status == 'on') {
		// 		$t = new TwitterController();
		// 		$t->send_search_query($data['feed_id']);
		// 		$job->delete();
		// 	}
		// });
		return Redirect::to('/view_feed/' .$feed_id);
	}

	public function stopFetching ($feed_id) {
		//turn feed status off
		DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->update(array('feed_status' => 'off'));
		return Redirect::to('/view_feed/' .$feed_id);
	}
}