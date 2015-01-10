<?php

class FeedController extends BaseController {

	public function getFeeds () {
		$feeds = DB::connection('mysql')->table('users_feeds')
			->join('feeds', 'users_feeds.feed_id', '=', 'feeds.id')
			->where('user_id', Auth::user()->id)
			->get();

		return View::make('feeds')
			->with('feeds', $feeds);
	}

	public function getEditFeed ($feed_id = null) {

		if ($feed_id){	
			$users_feeds = DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->first();
			$feeds = DB::connection('mysql')->table('feeds')
				->where('id', $feed_id)
				->orderBy('created_at', 'desc')->first();
            $transformations = DB::connection('mysql')->table('transformations_feeds')->where('feed_id', $feed_id)->lists('transformation');
            $transformations_params = DB::connection('mysql')->table('transformations_feeds')->where('feed_id', $feed_id)->lists('params');
            $views = DB::connection('mysql')->table('views_feeds')->where('feed_id', $feed_id)->lists('view');
            $views_params = DB::connection('mysql')->table('views_feeds')->where('feed_id', $feed_id)->lists('params');


//            echo print_r($transformations);
//            echo print_r($transformations_params);
//            echo array_search('calais', $transformations);
//            die();

			return View::make('edit_feed')
				->with('feed_id', $users_feeds->feed_id)
				->with('name', $feeds->feed_name)
				->with('status', $feeds->feed_status)
				->with('params', $feeds->params)
				->with('new_feed', false)
                ->with('calais', (in_array('calais', $transformations) ? true : false))
                ->with('calais_params', (in_array('calais', $transformations) ? $transformations_params[array_search('calais', $transformations)] : ''))
                ->with('sutime', (in_array('sutime', $transformations) ? true : false))
                ->with('sutime_params', (in_array('sutime', $transformations) ? $transformations_params[array_search('sutime', $transformations)] : ''))

                ->with('data_overview', (in_array('data_overview', $views) ? true : false))
                ->with('data_overview_params', (in_array('data_overview', $views) ? $views_params[array_search('data_overview', $views)] : ''))
                ->with('alerts_overview', (in_array('alerts_overview', $views) ? true : false))
                ->with('alerts_overview_params', (in_array('alerts_overview', $views) ? $views_params[array_search('alerts_overview', $views)] : ''))
                ->with('alerts_timeline', (in_array('alerts_timeline', $views) ? true : false))
                ->with('alerts_timeline_params', (in_array('alerts_timeline', $views) ? $views_params[array_search('alerts_timeline', $views)] : ''));
		}
		else {
			return View::make('edit_feed')
				->with('feed_id', '')
				->with('name', '')
				->with('status', '')
				->with('params', '')
				->with('method', 'post')
				->with('new_feed', true)
                ->with('calais', false)
                ->with('calais_params', '')
                ->with('sutime', false)
                ->with('sutime_params', '')
                ->with('data_overview', false)
                ->with('data_overview_params', '')
                ->with('alerts_overview', false)
                ->with('alerts_overview_params', '')
                ->with('alerts_timeline', false)
                ->with('alerts_timeline_params', '');
		}
	}

	public function postEditFeed ($feed_id = null) {
		
		if ($feed_id) {
			DB::connection('mysql')->table('feeds')->where('id', $feed_id)->update(array(
				'feed_name' => Input::get('name'),
				'feed_status' => Input::get('status'),
				'params' => Input::get('params'),
			));


            foreach(Input::only('calais', 'sutime') as $transformation=>$val) {
                DB::connection('mysql')->table('transformations_feeds')->where('feed_id', $feed_id)->where('transformation', $transformation)->delete();
                if ($transformation==$val) {
                    DB::connection('mysql')->table('transformations_feeds')->insert(array(
                        'feed_id' => $feed_id,
                        'transformation' => $transformation,
                        'params' => Input::get($transformation . '_params'),
                    ));
                }
            }

            foreach(Input::only('data_overview', 'alerts_overview', 'alerts_timeline') as $view=>$val) {
                DB::connection('mysql')->table('views_feeds')->where('feed_id', $feed_id)->where('view', $view)->delete();
                if ($view == $val) {
                    DB::connection('mysql')->table('views_feeds')->insert(array(
                        'feed_id' => $feed_id,
                        'view' => $view,
                        'params' => Input::get($view . '_params'),
                    ));
                }
            }

			return Redirect::to('/view_feed/'.$feed_id);

		} else {

			$feed_id = DB::connection('mysql')->table('users_feeds')->insertGetId(array(
				'user_id' => Auth::user()->id));

			DB::connection('mysql')->table('feeds')->insert(array(
				'id' => $feed_id,
				'params' => Input::get('params'),
				'feed_name' => Input::get('name'),
				'feed_status' => 0,
                'type' => 'twitter',
				'created_at' => new DateTime));

            foreach(Input::only('calais', 'sutime') as $transformation=>$val) {
                if ($transformation == $val) {
                    DB::connection('mysql')->table('transformations_feeds')->Insert(array(
                        'feed_id' => $feed_id,
                        'transformation' => $transformation,
                        'params' => Input::get($transformation . '_params'),
                    ));
                }
            }

            foreach(Input::only('data_overview', 'alerts_overview', 'alerts_timeline') as $view=>$val) {
                if ($view == $val) {
                    DB::connection('mysql')->table('views_feeds')->Insert(array(
                        'feed_id' => $feed_id,
                        'view' => $view,
                        'params' => Input::get($view . '_params'),
                    ));
                }
            }

			return Redirect::to('/view_feed/'.$feed_id);			
		}
	}

	public function getDeleteFeed ($feed_id) {
		DB::connection('mongodb')->collection('data1')->where('_id', $feed_id)->delete();
        DB::connection('mysql')->table('feeds')->where('id', $feed_id)->delete();
        DB::connection('mysql')->table('users_feeds')->where('feed_id', $feed_id)->delete();
        DB::connection('mysql')->table('views_feeds')->where('feed_id', $feed_id)->delete();
		return Redirect::to('feeds');
	}


	public function getViewFeed($feed_id, $page_num=1) {

		$feed_data = $this->getFeedData($feed_id, $page_num);

		return View::make('feed')
			// FEED DATA
			->with('feeds', $feed_data['feeds'])
			->with('feed', $feed_data['feed'])
			->with('total_records', $feed_data['total_records'])
			->with('tweets', $feed_data['data'])
			->with('take', $feed_data['take'])
			->with('page_num', $page_num);
	}




	private function getFeedData ($feed_id, $page_num, $results_per_page=5) {
		// $page_num should get a default value of 1

		$data = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))
                                         ->orderBy('datetime', 'desc', 'natural')->skip($page_num * $results_per_page - 1)->take($results_per_page)->get();
	    $total_records = DB::connection('mongodb')->collection('data1')->whereIn('feeds', array($feed_id))->count();

		$feed = DB::connection('mysql')->table('users_feeds')
			->join('feeds', 'users_feeds.feed_id', '=', 'feeds.id')
			->where('feed_id', $feed_id)->first();
		$feeds = DB::connection('mysql')->table('users_feeds')
			->join('feeds', 'users_feeds.feed_id', '=', 'feeds.id')
			->where('user_id', Auth::user()->id)
			->select('feed_id','feed_name')->get();

		return array(
			'feeds' => $feeds,
			'feed' => $feed,
			'total_records' => $total_records,
			'data' => $data,
			'take' => $results_per_page
		);
	}

    public function getFeedDataJson ($feed_id, $page_num, $results_per_page=5) {
        // $page_num should get a default value of 1

        return json_encode($this->getFeedData($feed_id, $page_num, $results_per_page));
    }


	public function showTweet ($id) {
		$db = DB::connection('mongodb')->getMongoDB();								
		$db_record = $db->execute('return db.data1.find({ _id : '. $id .'}).toArray();');
		echo Pre::render($db_record);
	}

	public function startFetching ($feed_id) {

		try {
            //turn feed status on
			DB::connection('mysql')->table('feeds')->where('id', $feed_id)->update(array('feed_status' => 1));

            //get initializer type
            $initializer_type = DB::connection('mysql')->table('feeds')->where('id', $feed_id)->first()->type;

            //push initializer onto the queue
			Queue::connection('PendingTwitterQueue')->push('QueueTasks@' . $initializer_type . 'Job', array('feed_id' => $feed_id));
		}
		catch (Exception $e) {
			Log::error($e);
		}
//		return Redirect::to('view_feed/' . $feed_id);
	}

    public function stopFetching ($feed_id) {
        //turn feed status off
        try {
            DB::connection('mysql')->table('feeds')->where('id', $feed_id)->update(array('feed_status' => 0));
        }
        catch (Exception $e) {
            Log::error($e);
        }
    }

    public function getFeedStatus($feed_id) {
        $status = DB::connection('mysql')->table('feeds')->where('id', $feed_id)->first()->feed_status;
        return $status;
    }

}