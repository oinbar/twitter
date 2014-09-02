<?php

class FeedController extends BaseController {

	public function getFeeds () {
		$feeds = DB::connection('mysql')->table('users_feeds')->where('user_id', Auth::user()->id)
			->select('feed_id','feed_name')->get();

		return View::make('feeds')
			->with('feeds', $feeds);
	}

	public function getEditFeed ($feed_id = null) {

		if ($feed_id){	
			//figure out how to do joins with eloquent and replace this garbage
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
				->with('new_feed', false);				
		}
		else {
			return View::make('edit_feed')
				->with('feed_id', '')
				->with('name', '')
				->with('status', '')
				->with('criteria', '')
				->with('update_rate', '')
				->with('method', 'post')
				->with('new_feed', true);				
		}				
	}

	public function postEditFeed ($feed_id = null) {
		
		if ($feed_id) {
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
			return Redirect::to('/view_feed/'.$feed_id);

		} else {
			$id = DB::connection('mysql')->table('users_feeds')->insertGetId(array(
				'user_id' => Auth::user()->id,
				'feed_name' => Input::get('name'),
				'feed_status' => 'off'));
			DB::connection('mysql')->table('feeds')->insert(array(
				'id' => $id,
				'update_rate' => Input::get('update_rate'),
				'criteria' => Input::get('criteria'),
				'created_at' => new DateTime));

			return Redirect::to('/view_feed/'.$id);			
		}
	}

	public function getDeleteFeed ($feed_id) {
		DB::connection('mongodb')->collection('data1')->where('_id', $feed_id)->delete();
		return Redirect::to('feeds');
	}

	public function getViewFeed ($feed_id, $skip = 0) {
		$take = 20; // numbe of results to select
		$data = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))->orderBy('datetime', 'desc', 'natural')->skip($skip)->take($take)->get();
	    $count = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))->count();

		$feed = DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->first();
		
		return View::make('feed')
		->with('feed', $feed)
		->with('num_records', $count)
		->with('data', $data)
		->with('feed_id', $feed_id)
		->with('start', $skip)
		->with('end', $skip + $take)
		->with('prev', max(0, $skip-$take));
	}

	public function showTweet ($id) {
		$db = DB::connection('mongodb')->getMongoDB();								
		$db_record = $db->execute('return db.data1.find({ _id : '. $id .'}).toArray();');
		echo Pre::render($db_record);
	}

	public function startFetching ($feed_id) {
		//turn feed status on
		DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->update(array('feed_status' => 'on'));

		Queue::connection('PendingTwitterQueue')->push('QueueTasks@searchTwitterFeedCriteriaJob', array('feed_id' => $feed_id)); 

		return Redirect::to('/view_feed/' . $feed_id);
	}

	public function stopFetching ($feed_id) {
		//turn feed status off
		DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->update(array('feed_status' => 'off'));
		return Redirect::to('/view_feed/' .$feed_id);
	}

	public function getAlerts ($feed_id) {
		
		date_default_timezone_set("EST");
		$current_date_time = date('Y-m-d H:i:s', time());

		$query = 
		'db.data1.aggregate([
	    { $match : { feeds : { $in : [ "' . $feed_id . '" ] }, 
                   "opencalais._type" : { $in : [ "City", "Facility" ] }, 
                   "SUTime.future" : { $exists : true },
                   
                   text : { $regex : /^((?!(yesterday)|(ago)).)*$/ } } },
	    { $unwind : "$opencalais" }, 
        { $unwind : "$SUTime" }, 
	    { $match : { "opencalais._type" : { $in : [ "City", "Facility" ] }, 
                   "SUTime.future" : {$exists : true} } },
	    { $project : { text : 1, 
                     opencalais : 1, 
                     SUTime : 1, 
                     retweet_count : 1, 
                     id : 1 } },
	    { $group: { _id : "$text" ,
                id : { $addToSet : "$id" },
                future_time_norm : { $first : "$SUTime.normalized" },
                future_time_original : { $first : "$SUTime.original" },
                location : { $addToSet : "$opencalais.name" },
                location_type : { $addToSet : "$opencalais._type" },
    	        retweet_count : { $max : "$retweet_count" } } },    
		]).toArray()';	
	
		try {			
			$db = DB::connection('mongodb')->getMongoDB();								
			$results = $db->execute('return ' . $query . ';');
			$temp_file_in = tempnam(__DIR__ . '/temp/', 'alertsAggIn');
			$temp_file_out = tempnam(__DIR__ . '/temp/', 'alertsAggOut');
			file_put_contents($temp_file_in, json_encode($results['retval']));
			
			exec(base_path() . '/../python_venv/bin/python ' . __DIR__ .  '/python_scripts/twitter_alerts_aggregator.py ' . $temp_file_in . ' ' . $temp_file_out . ' 2>&1', $err);
			if ($err){
				throw new Exception(Pre::render($err));
			}			
			echo file_get_contents($temp_file_out);

			unset($temp_file_in);
			unset($temp_file_out);			
		}		
		catch (Exception $e){
			Log::error('ALERTS AGGREGATOR :  '. $e);
			echo $e;
		}
	}
}