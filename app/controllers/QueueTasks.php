<?php

include "ProcessingTasks.php";

class QueueTasks {
	
	public function searchTwitterFeedCriteriaJob($job, $data){
		/*
		The twitter fetch job.  The job proceeds if its status is labeled "ON" in the db.  The job is released back onto the queue
		after a time delay, to regulate the rate of API calls.  Each job is tied to a feed, and currently they all share the same API
		key.  To provide scalability, an individual API key should be associated with each feed.

		NOTE: eventually, this job should also be run continuously, and decoupled from an http trigger.  There is also danger of 
		triggering too many jobs at once causing API overload.  For now stick with one active feed, and be weary about turning it on and off too
		quickly.
		*/

		Log::error('TWITTER QUEUE JOB');

		$feed_status = DB::connection('mysql')->table('feeds')->where('feed_id', $data['feed_id'])->first()->feed_status;

		$user_twitter_credentials = DB::select(DB::raw(
			'select twitter_oauth_access_token, twitter_oauth_access_token_secret
			from users_feeds
			left join feeds on users_feeds.feed_id = feeds.id
			left join users on users_feeds.user_id = users.id
			where feed_id = 5
			limit 1'
		));		
		$access_token = get_object_vars($user_twitter_credentials['0'])['twitter_oauth_access_token'];
		$access_token_secret = 	get_object_vars($user_twitter_credentials['0'])['twitter_oauth_access_token_secret'];

		$num_active_feeds_per_user = DB::select(DB::raw(
			'select count(distinct user_id, feed_id, feed_status) as count
			from users_feeds
			left join feeds on users_feeds.feed_id = feeds.id			
			where user_id = ' . $data['feed_id'] . ' and feed_status = 1'
			));		
		$num_active_feeds_per_user = get_object_vars($num_active_feeds_per_user['0'])['count'];

		if ($feed_status == 1) {
			$p = new ProcessingTasks();
			$p->searchTwitterFeedCriteria($data['feed_id'],
												'PendingCalaisList',
												$access_token,
												$access_token_secret);		

			$job->release(10 * $num_active_feeds_per_user);
			
		} 
		else {
			$job->delete();
		}
	} 

	public function runJsonThroughCalaisJob ($job, $data) {
		/*
		The open calais fetch job.  The job is released immediately back onto the queue since time delays are taken care of
		inside the function.  Currently this job uses one API key, and this job takes care of all data going to open calais 
		(regardless of feed).  To provide scalability, more jobs should be triggered, each with a different API key.
		*/
		$p = new ProcessingTasks();
		$p->runJsonThroughCalais($data['calais_key']);

		$job->release();
	}

	public function runJsonThroughSUTimeJob ($job, $data) {
		/*
		The SUTime job.  The job is released immediately back onto the queue.  There is significant overhead in loading the 
		JVM and related libraries, as currently this is performed each time the jar is called.
		*/
		$p = new ProcessingTasks();
		$p->runJsonThroughSUTime();

		$job->release();
	}

	public function insertJsonToDBJob ($job, $data) {
		/*
		The persistence job (moving data from cache to the db).  The job is released immediately back onto the queue since the only
		delay imposed is the IO of the database.
		*/
		$p = new ProcessingTasks();
		$p->insertJsonToDB();

		$job->release();
	}
}
