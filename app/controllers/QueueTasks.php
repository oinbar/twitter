<?php

include "TwitterController.php";

class QueueTasks {
	public function send_search_query($job, $data){

		$t = new TwitterController();
		$t->send_search_query($data['feed_id']);

		$job->release(10);
	}
}
