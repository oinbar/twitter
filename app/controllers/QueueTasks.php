<?php

include "TwitterController.php";

class QueueTasks {
	public function send_search_query($job, $data){

		$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;
		
		if ($feed_status == 'on') {
			$t = new TwitterController();
			$t->send_search_query($data['feed_id']);
			$job->release(10);
		} else {
			$job->delete();
		}
	}

	public function simple ($job, $data){

			$t = new TwitterController();
			$t->send_search_query($data['feed_id']);
			$job->delete();
	}

	public function simple2 ($job, $data){

			$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;

			$t = new TwitterController();
			$t->send_search_query($data['feed_id']);
			$job->delete();
	}

	public function simple2 ($job, $data){
			
			$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;

			$t = new TwitterController();
			$t->send_search_query($data['feed_id']);
			$job->delete();

			echo $feed_status;
	}
}
