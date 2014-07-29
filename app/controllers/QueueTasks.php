<?php

include "TwitterController.php";

class QueueTasks {
	public function send_search_query($job, $data){

		// var_dump(Pre::render($feed_id));
		// die();


		$t = new TwitterController();
		$t->send_search_query($data['feed_id']);

		$job->release(10);
	}
}
