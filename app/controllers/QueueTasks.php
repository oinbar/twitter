<?php

include "TwitterController.php";

class QueueTasks {
	public function send_search_query($job, $data){

		$feed_status = DB::connection('mysql')->table('users_feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;

		error_log('feed_status '. $feed_status, 3, '~/app-root/logs/php.log')


		if ($feed_status == 'on') {

			error_log('feed status check ok' , 3, '~/app-root/logs/php.log');

			try{
				$t = new TwitterController();
				$t->send_search_query($data['feed_id']);

			catch (Exception $e) {
				error_log($e , 3, '~/app-root/logs/php.log');
			}

			$job->release(10);
			} 
		else {
			$job->delete();
		}
		error_log('job released' , 3, '~/app-root/logs/php.log');
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
