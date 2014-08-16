<?php

include "ProcessingTasks.php";

class QueueTasks {
	
	public function send_search_query($job, $data){
		/*
		The twitter fetch job.  The job proceeds if its status is labeled "ON" in the db.  The job is released back onto the queue
		after a time delay, to regulate the rate of API calls.  Each job is tied to a feed, and currently they all share the same API
		key.  To provide scalability, an individual API key should be associated with each feed.

		NOTE: eventually, this job should also be run continuously, and decoupled from an http trigger.  There is also danger of 
		triggering too many jobs at once causing API overload.  For now stick with one active feed, and be weary about turning it on and off too
		quickly.
		*/
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

	public function send_tweet_to_calais ($job, $data) {
		/*
		The open calais fetch job.  The job is released immediately back onto the queue since time delays are taken care of
		inside the function.  Currently this job uses one API key, and this job takes care of all data going to open calais 
		(regardless of feed).  To provide scalability, more jobs should be triggered, each with a different API key.
		*/
		$p = new ProcessingTasks();
		$p->send_tweet_to_calais();

		$job->release();
	}

	public function cache_to_db ($job, $data) {
		/*
		The persistence job (moving data from cache to the db).  The job is released immediately back onto the queue since the only
		delay imposed is the IO of the database.
		*/
		$p = new ProcessingTasks();
		$p->cache_to_db();

		$job->release();

	}
}
