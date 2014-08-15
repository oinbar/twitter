<?php

include "ProcessingTasks.php";

class QueueTasks {
	
	public function send_search_query($job, $data){

		$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;
		
		if ($feed_status == 'on') {
			$p = new ProcessingTasks();
			$p->send_search_query($data['feed_id']);
			$job->delete();

			$job->release(10);
			
		} else {
			$job->delete();
		}
	}

	public function cache_to_db ($job, $data) {

		$p = new ProcessingTasks();
		$p->cache_to_db($data['cache_list'], $data['batch_size']);

		$job->release(10);

	}
}
